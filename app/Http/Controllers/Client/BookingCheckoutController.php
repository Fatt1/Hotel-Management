<?php

namespace App\Http\Controllers\Client;

use App\Actions\Customers\GetCustomerByEmailAction;

use App\Http\Controllers\Controller;
use App\Mail\BookingSuccessMail;
use App\Models\RoomType;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Payment;
use App\Actions\Payments\ProcessMoMoPaymentAction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BookingCheckoutController extends Controller
{
    /**
     * Hiển thị trang nhập thông tin khách + tóm tắt đặt phòng.
     * Nhận dữ liệu phòng đã chọn từ trang /rooms (POST).
     */
    public function checkout(Request $request)
    {
        // --- Ngày & số khách ---
        $checkIn    = $request->input('check_in',  now()->format('Y-m-d'));
        $checkOut   = $request->input('check_out', now()->addDays(3)->format('Y-m-d'));
        $adults     = max(1, (int) $request->input('adults', 2));
        $children   = max(0, (int) $request->input('children', 0));
        $roomsCount = max(1, (int) $request->input('rooms_count', 1));

        try {
            $checkInDate  = Carbon::parse($checkIn);
            $checkOutDate = Carbon::parse($checkOut);
            if ($checkInDate->gte($checkOutDate)) {
                $checkOutDate = $checkInDate->copy()->addDay();
            }
        } catch (\Throwable) {
            $checkInDate  = now();
            $checkOutDate = now()->addDays(3);
        }
        $checkIn  = $checkInDate->format('Y-m-d');
        $checkOut = $checkOutDate->format('Y-m-d');
        $nights   = max(1, (int) $checkInDate->diffInDays($checkOutDate));

        // --- Các phòng đã chọn (qty_{id} => số lượng) ---
        // Rooms page POSTs: qty_1=2, qty_5=1, ... kèm check_in, check_out, adults, children, rooms_count
        $selectedRooms = [];
        $subtotal = 0;

        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'qty_') && (int) $value > 0) {
                $rtId = (int) substr($key, 4);
                $rt   = RoomType::with(['images'])->find($rtId);
                if ($rt) {
                    $qty   = (int) $value;
                    $price = (float) $rt->daily_price;
                    $selectedRooms[] = [
                        'room_type'  => $rt,
                        'qty'        => $qty,
                        'price'      => $price,
                        'line_total' => $price * $qty * $nights,
                    ];
                    $subtotal += $price * $qty * $nights;
                }
            }
        }

        // Nếu không có phòng nào được chọn → redirect về trang rooms
        if (empty($selectedRooms)) {
            return redirect()->route('client.rooms.index', [
                'check_in'    => $checkIn,
                'check_out'   => $checkOut,
                'adults'      => $adults,
                'children'    => $children,
                'rooms_count' => $roomsCount,
            ])->with('error', 'Vui lòng chọn ít nhất một phòng trước khi đặt.');
        }

        return view('client.booking.checkout', compact(
            'selectedRooms', 'subtotal',
            'checkIn', 'checkOut', 'checkInDate', 'checkOutDate',
            'adults', 'children', 'roomsCount', 'nights'
        ));
    }

    /**
     * Hiển thị trang chọn phương thức thanh toán (Step 3/3).
     * Nhận dữ liệu từ form thông tin khách (checkout page).
     */
    public function payment(Request $request)
    {
        // Reparse dates and counts
        $checkIn    = $request->input('check_in',  now()->format('Y-m-d'));
        $checkOut   = $request->input('check_out', now()->addDays(3)->format('Y-m-d'));
        $adults     = max(1, (int) $request->input('adults', 2));
        $children   = max(0, (int) $request->input('children', 0));
        $roomsCount = max(1, (int) $request->input('rooms_count', 1));

        try {
            $checkInDate  = Carbon::parse($checkIn);
            $checkOutDate = Carbon::parse($checkOut);
            if ($checkInDate->gte($checkOutDate)) {
                $checkOutDate = $checkInDate->copy()->addDay();
            }
        } catch (\Throwable) {
            $checkInDate  = now();
            $checkOutDate = now()->addDays(3);
        }
        $checkIn  = $checkInDate->format('Y-m-d');
        $checkOut = $checkOutDate->format('Y-m-d');
        $nights   = max(1, (int) $checkInDate->diffInDays($checkOutDate));

        // Guest info passed through from step 2
        $guestInfo = [
            'first_name' => $request->input('first_name', ''),
            'last_name'  => $request->input('last_name', ''),
            'country'    => $request->input('country', 'VN'),
            'phone_code' => $request->input('phone_code', '+84'),
            'phone'      => $request->input('phone', ''),
        ];

        // Selected rooms
        $selectedRooms = [];
        $subtotal = 0;

        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'qty_') && (int) $value > 0) {
                $rtId = (int) substr($key, 4);
                $rt   = RoomType::with(['images'])->find($rtId);
                if ($rt) {
                    $qty   = (int) $value;
                    $price = (float) $rt->daily_price;
                    $selectedRooms[] = [
                        'room_type'  => $rt,
                        'qty'        => $qty,
                        'price'      => $price,
                        'line_total' => $price * $qty * $nights,
                    ];
                    $subtotal += $price * $qty * $nights;
                }
            }
        }

        if (empty($selectedRooms)) {
            return redirect()->route('client.rooms.index');
        }

        // All request inputs to pass through to the final booking form
        $allInputs = $request->except(['_token']);

        return view('client.booking.payment', compact(
            'selectedRooms', 'subtotal', 'allInputs', 'guestInfo',
            'checkIn', 'checkOut', 'checkInDate', 'checkOutDate',
            'adults', 'children', 'roomsCount', 'nights'
        ));
    }

    /**
     * Nhận form thanh toán, tạo booking reference, lưu session, redirect đến trang xác nhận.
     */
    public function confirm(Request $request)
    {
        // Generate booking reference
        $bookingRef = 'UL-' . strtoupper(substr(md5(uniqid()), 0, 8));

        // Parse core data
        $checkIn  = $request->input('check_in',  now()->format('Y-m-d'));
        $checkOut = $request->input('check_out', now()->addDays(3)->format('Y-m-d'));
        $adults   = max(1, (int) $request->input('adults', 2));
        $children = max(0, (int) $request->input('children', 0));

        try {
            $checkInDate  = Carbon::parse($checkIn);
            $checkOutDate = Carbon::parse($checkOut);
        } catch (\Throwable) {
            $checkInDate  = now();
            $checkOutDate = now()->addDays(3);
        }
        $nights = max(1, (int) $checkInDate->diffInDays($checkOutDate));

        $selectedRooms = [];
        $subtotal = 0;
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'qty_') && (int) $value > 0) {
                $rtId = (int) substr($key, 4);
                $rt   = RoomType::with(['images'])->find($rtId);
                if ($rt) {
                    $qty   = (int) $value;
                    $price = (float) $rt->daily_price;
                    $selectedRooms[] = [
                        'id'         => $rt->id,
                        'name'       => $rt->name,
                        'price'      => $price,
                        'image_url'  => $this->resolveImageUrl($rt->images->first()?->image_url),
                        'width'      => $rt->width ?? 0,
                        'qty'        => $qty,
                        'line_total' => $price * $qty * $nights,
                    ];
                    $subtotal += $price * $qty * $nights;
                }
            }
        }

        $bookingData = [
            'check_in'    => $checkInDate->format('Y-m-d H:i:s'),
            'check_out'   => $checkOutDate->format('Y-m-d H:i:s'),
            'adults'      => $adults,
            'children'    => $children,
            'nights'      => $nights,
            'rooms'       => $selectedRooms,
            'subtotal'    => $subtotal,
            'guest_name'  => trim($request->input('first_name', '') . ' ' . $request->input('last_name', '')),
            'guest_email' => $request->input('email_verify', ''),
            'guest_phone' => $request->input('phone', ''),
            'payment'     => $request->input('payment_method', 'momo'),
            'confirmed_at'=> now()->format('d/m/Y H:i'),
        ];

        // Lấy danh sách phòng đã kẹt lịch
        $bookedRoomIds = BookingDetail::join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
            ->whereIn('bookings.status', ['Chờ xác nhận', 'Đã đặt', 'Đang ở'])
            ->where('booking_details.checkin_date', '<', $checkOut)
            ->where('booking_details.checkout_date', '>', $checkIn)
            ->pluck('booking_details.room_id')
            ->toArray();

        // Transaction database để tạo Booking
        try {
            DB::beginTransaction();

            $customer = Customer::where('email', $bookingData['guest_email'])->first();
            if (!$customer) {
                $customer = new Customer();
                $customer->email        = $bookingData['guest_email'];
                $customer->first_name   = $request->input('first_name', '');
                $customer->last_name    = $request->input('last_name', '');
                $customer->phone_number = $bookingData['guest_phone'];
                $customer->country      = $request->input('country', 'VN');
                $customer->password     = bcrypt(uniqid());
                $customer->save();
            }

            $booking = Booking::create([
                'customer_id'          => $customer->id,
                'booking_date'         => now(),
                'checkin_date'         => $bookingData['check_in'],
                'checkout_date'        => $bookingData['check_out'],
                'status'               => 'Chờ xác nhận',
                'total_room_amount'    => $subtotal,
                'total_service_amount' => 0,
                'surcharge_amount'     => 0,
                'final_amount'         => $subtotal,
            ]);

            foreach ($selectedRooms as $roomData) {
                // Tìm các phòng còn trống thuộc loại $roomData['id']
                $availableRooms = \App\Models\Room::where('room_type_id', $roomData['id'])
                    ->where('status', 'ready')
                    ->whereNotIn('id', $bookedRoomIds)
                    ->limit($roomData['qty'])
                    ->get();

                if ($availableRooms->count() < $roomData['qty']) {
                    DB::rollBack();
                    return back()->with('error', 'Một số loại phòng bạn chọn hiện không đủ phòng trống. Xin hãy thử lại.');
                }

                foreach ($availableRooms as $availRoom) {
                    BookingDetail::create([
                        'booking_id'       => $booking->id,
                        'room_id'          => $availRoom->id,
                        'checkin_date'     => $bookingData['check_in'],
                        'checkout_date'    => $bookingData['check_out'],
                        'checkout_status'  => false,
                        'hourly_price'     => 0,
                        'daily_price'      => $roomData['price'],
                        'room_amount'      => $roomData['price'] * $nights,
                        'service_amount'   => 0,
                        'surcharge_amount' => 0,
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DB Booking Create Error: ' . $e->getMessage());
            return redirect()->route('client.rooms.index')->with('error', 'Lỗi khởi tạo lưu trữ đơn hàng: ' . $e->getMessage());
        }

        // Nếu là phương thức thanh toán MoMo thì tạo link redirect
        if ($bookingData['payment'] === 'momo') {
            $momoAction = app(ProcessMoMoPaymentAction::class);
            $payUrl = $momoAction->execute($booking, $subtotal);

            if (!empty($payUrl)) {
                return redirect()->away($payUrl);
            } else {
                return redirect()->route('client.rooms.index')->with('error', 'Lỗi kết nối đến cổng thanh toán MoMo. Tra cứu file log để xem nguyên nhân.');
            }
        }

        return redirect()->route('client.booking.confirmation')->with('success', 'Đặt phòng thành công (Chuyển khoản).');
    }

    /**
     * Return URL sau khi quẹt mã QR MoMo chuyển hướng user về
     */
    public function momoReturn(Request $request)
    {
        $resultCode = $request->query('resultCode');
        $orderId    = $request->query('orderId');

        if ($resultCode === '0') {
            // Thanh toán thành công, hiển thị trang hoàn tất
            $bookingId = explode('_', $orderId)[0];
            $booking = Booking::with('customer')->find($bookingId);

            return view('client.booking.confirmation', [
                'success' => true,
                'booking' => $booking,
                'message' => 'Thanh toán MoMo thành công! Mã đơn hàng của bạn là: ' . $bookingId
            ]);
        } else {
            // Thanh toán thất bại hoặc user bấm hủy
            return view('client.booking.confirmation', [
                'success' => false,
                'message' => 'Giao dịch MoMo thất bại hoặc đã bị hủy (Mã Lỗi: ' . $resultCode . ')'
            ]);
        }
    }

    /**
     * IPN Webhook cho MoMo phía server
     */
    public function momoIpn(Request $request)
    {
        $secretKey = env('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa');
        
        $partnerCode = $request->input('partnerCode');
        $orderId     = $request->input('orderId');
        $requestId   = $request->input('requestId');
        $amount      = $request->input('amount');
        $orderInfo   = $request->input('orderInfo');
        $orderType   = $request->input('orderType');
        $transId     = $request->input('transId');
        $resultCode  = $request->input('resultCode');
        $message     = $request->input('message');
        $payType     = $request->input('payType');
        $responseTime= $request->input('responseTime');
        $extraData   = $request->input('extraData');
        $momoSignature = $request->input('signature');

        // Công thức tạo rawHash IPN chuẩn KHÔNG có accessKey:
        $rawHash = "amount=" . $amount .
                   "&extraData=" . $extraData .
                   "&message=" . $message .
                   "&orderId=" . $orderId .
                   "&orderInfo=" . $orderInfo .
                   "&orderType=" . $orderType .
                   "&partnerCode=" . $partnerCode .
                   "&payType=" . $payType .
                   "&requestId=" . $requestId .
                   "&responseTime=" . $responseTime .
                   "&resultCode=" . $resultCode .
                   "&transId=" . $transId;

        $mySignature = hash_hmac("sha256", $rawHash, $secretKey);

        if ($mySignature !== $momoSignature) {
            // Chữ ký fake, trả về 401
            return response()->json(['message' => 'Invalid Signature'], 401);
        }

        if ($resultCode == 0) {
            $bookingId = explode('_', $orderId)[0];
            $booking = Booking::find($bookingId);

            if ($booking) {
                // Update booking status
                $booking->status = 'Hoàn tất'; // Hoặc 'Đã thanh toán', tuỳ cấu hình enum dự án
                $booking->save();

                // Lưu table payments
                Payment::create([
                    'booking_id'       => $booking->id,
                    'amount'           => $amount,
                    'payment_method'   => 'momo',
                    'transaction_code' => $transId,
                    'note'             => $message,
                ]);
            }
        }

        return response()->noContent(); 
    }

    /**
     * Hiển thị trang xác nhận đặt phòng thành công.
     */
    public function confirmation()
    {
        $bookingRef  = session('booking_ref');
        $bookingData = session('booking_data');

        if (! $bookingRef || ! $bookingData) {
            return redirect()->route('client.rooms.index');
        }

        try {
            $checkInDate  = Carbon::parse($bookingData['check_in']);
            $checkOutDate = Carbon::parse($bookingData['check_out']);
        } catch (\Throwable) {
            $checkInDate  = now();
            $checkOutDate = now()->addDays(3);
        }

        return view('client.booking.confirmation', compact('bookingRef', 'bookingData', 'checkInDate', 'checkOutDate'));
    }

    /**
     * Nhận email từ ajax, xác thực khách hàng để tự điền thông tin.
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(Request $request, GetCustomerByEmailAction $action)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $customer = $action->handle($request->input('email'));

        if ($customer) {
            return response()->json([
                'success' => true,
                'data' => [
                    'first_name' => $customer->first_name,
                    'last_name'  => $customer->last_name,
                    'phone'      => $customer->phone,
                    'country'    => $customer->country ?? '',
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy tài khoản. Vui lòng điền thông tin bên dưới để tạo mới.'
        ], 404);
    }

    /**
     * Convert DB image path to browser-usable URL.
     */
    private function resolveImageUrl(?string $imageUrl): string
    {
        if (!is_string($imageUrl) || trim($imageUrl) === '') {
            return '';
        }

        $imageUrl = trim($imageUrl);
        if (
            str_starts_with($imageUrl, 'http://') ||
            str_starts_with($imageUrl, 'https://') ||
            str_starts_with($imageUrl, '//')
        ) {
            return $imageUrl;
        }

        return asset('storage/' . ltrim($imageUrl, '/'));
    }
}
