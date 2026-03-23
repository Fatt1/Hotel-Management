<?php

namespace App\Http\Controllers\Client;

use App\Actions\Bookings\CreateBookingAction;
use App\Actions\Customers\GetCustomerByEmailAction;
use App\Data\BookingData;

use App\Http\Controllers\Controller;
use App\Mail\BookingSuccessMail;
use App\Models\RoomType;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Payment;
use App\Actions\Payments\ProcessMoMoPaymentAction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
        // If redirected back from confirm(), use flashed old input to rebuild the payment page.
        $payload = $request->all();
        if ($request->isMethod('get') && empty($payload)) {
            $payload = session('_old_input', []);
        }

        if (empty($payload)) {
            return redirect()->route('client.rooms.index');
        }

        // Reparse dates and counts
        $checkIn    = $payload['check_in'] ?? now()->format('Y-m-d');
        $checkOut   = $payload['check_out'] ?? now()->addDays(3)->format('Y-m-d');
        $adults     = max(1, (int) ($payload['adults'] ?? 2));
        $children   = max(0, (int) ($payload['children'] ?? 0));
        $roomsCount = max(1, (int) ($payload['rooms_count'] ?? 1));

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
            'first_name' => $payload['first_name'] ?? '',
            'last_name'  => $payload['last_name'] ?? '',
            'country'    => $payload['country'] ?? 'VN',
            'phone_code' => $payload['phone_code'] ?? '+84',
            'phone'      => $payload['phone'] ?? '',
        ];

        // Selected rooms
        $selectedRooms = [];
        $subtotal = 0;

        foreach ($payload as $key => $value) {
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
        $allInputs = collect($payload)->except(['_token'])->toArray();

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
        $request->validate([
            'email_verify' => ['required', 'email'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
        ], [
            'email_verify.required' => 'Vui long nhap email de tiep tuc thanh toan.',
            'email_verify.email' => 'Email khong dung dinh dang.',
            'first_name.required' => 'Vui long nhap ten.',
            'last_name.required' => 'Vui long nhap ho.',
            'phone.required' => 'Vui long nhap so dien thoai.',
        ]);

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
            'guest_email' => trim((string) $request->input('email_verify', '')),
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

        // Tạo booking (và booking_details) trước, sau đó mới gọi MoMo
        try {
            $bookingDetailsPayload = [];

            foreach ($selectedRooms as $roomData) {
                // Tìm các phòng còn trống thuộc loại $roomData['id']
                $availableRooms = \App\Models\Room::where('room_type_id', $roomData['id'])
                    ->where('status', 'ready')
                    ->whereNotIn('id', $bookedRoomIds)
                    ->limit($roomData['qty'])
                    ->get();

                if ($availableRooms->count() < $roomData['qty']) {
                    return back()->withInput()->with('error', 'Một số loại phòng bạn chọn hiện không đủ phòng trống. Xin hãy thử lại.');
                }

                foreach ($availableRooms as $availRoom) {
                    $bookingDetailsPayload[] = [
                        'room_id' => $availRoom->id,
                        'checkin_date' => $bookingData['check_in'],
                        'checkout_date' => $bookingData['check_out'],
                        'services' => [],
                    ];
                }
            }

            $booking = app(CreateBookingAction::class)->execute(new BookingData(
                email: $bookingData['guest_email'],
                phone_number: $bookingData['guest_phone'],
                first_name: $request->input('first_name', ''),
                last_name: $request->input('last_name', ''),
                country: $request->input('country', 'VN'),
                booking_date: new \DateTime(),
                booking_details: $bookingDetailsPayload,
                status: 'Chờ xác nhận',
                payment: null,
            ));
        } catch (\Throwable $e) {
            Log::error('DB Booking Create Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Loi khoi tao don hang. Vui long kiem tra lai thong tin va thu lai.');
        }

        // Nếu là phương thức thanh toán MoMo thì tạo link redirect
        if ($bookingData['payment'] === 'momo') {
            $momoAction = app(ProcessMoMoPaymentAction::class);
            $payUrl = $momoAction->execute($booking, $subtotal);

            if (!empty($payUrl)) {
                return redirect()->away($payUrl);
            } else {
                return back()->withInput()->with('error', 'Khong tao duoc lien ket thanh toan MoMo. Vui long kiem tra cau hinh MoMo/NGROK va thu lai.');
            }
        }

        return redirect()->route('client.booking.confirmation')->with('success', 'Đặt phòng thành công (Chuyển khoản).');
    }

    /**
     * Return URL sau khi quẹt mã QR MoMo chuyển hướng user về
     */
    public function momoReturn(Request $request)
    {
        $resultCode = (string) $request->query('resultCode', '');
        $orderId    = (string) $request->query('orderId', '');
        $bookingId  = (int) explode('_', $orderId)[0];

        Log::info('MoMo return payload', [
            'query' => $request->query(),
        ]);

        $booking = Booking::with(['customer', 'bookingDetails.room.roomType.images', 'payments'])->find($bookingId);

        $isPaid = (bool) ($booking?->payments?->contains(function ($payment) {
            return $payment->payment_method === 'momo';
        }) ?? false);

        $rooms = [];
        $subtotal = 0;
        $checkInDate = now();
        $checkOutDate = now()->addDay();

        if ($booking && $booking->bookingDetails->isNotEmpty()) {
            foreach ($booking->bookingDetails as $detail) {
                $lineTotal = (float) ($detail->room_amount ?? 0);
                if ($lineTotal <= 0) {
                    $lineTotal = (float) (($detail->daily_price ?? 0) * max(1, Carbon::parse($detail->checkin_date)->diffInDays(Carbon::parse($detail->checkout_date))));
                }

                $subtotal += $lineTotal;
                $rooms[] = [
                    'name' => $detail->room?->roomType?->name ?? ('Phong #' . $detail->room_id),
                    'qty' => 1,
                    'line_total' => $lineTotal,
                    'width' => $detail->room?->roomType?->width ?? 0,
                    'image_url' => $detail->room?->roomType?->images?->first()?->image_url ?? '',
                ];
            }

            $checkInDate = Carbon::parse($booking->bookingDetails->min('checkin_date'));
            $checkOutDate = Carbon::parse($booking->bookingDetails->max('checkout_date'));
        }

        $bookingRef = 'UL-' . str_pad((string) $bookingId, 6, '0', STR_PAD_LEFT);
        $bookingData = [
            'check_in' => $checkInDate->format('Y-m-d H:i:s'),
            'check_out' => $checkOutDate->format('Y-m-d H:i:s'),
            'adults' => 2,
            'children' => 0,
            'nights' => max(1, $checkInDate->diffInDays($checkOutDate)),
            'rooms' => $rooms,
            'subtotal' => $subtotal,
            'guest_name' => trim(($booking?->customer?->first_name ?? '') . ' ' . ($booking?->customer?->last_name ?? '')),
            'guest_email' => $booking?->customer?->email ?? '',
            'confirmed_at' => now()->format('d/m/Y H:i'),
        ];

        $success = $resultCode === '0';
        $message = $success
            ? ($isPaid
                ? ('Thanh toan MoMo thanh cong! Ma don hang cua ban la: ' . $bookingId)
                : ('Giao dich thanh cong tren MoMo, dang cho xac nhan thanh toan tu he thong (IPN).'))
            : ('Giao dich MoMo that bai hoac da bi huy (Ma Loi: ' . $resultCode . ').');

        return view('client.booking.confirmation', compact(
            'bookingRef', 'bookingData', 'checkInDate', 'checkOutDate', 'success', 'isPaid', 'message'
        ));
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

        Log::info('MoMo IPN payload', [
            'payload' => $request->all(),
        ]);

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
            Log::warning('MoMo IPN invalid signature', [
                'order_id' => $orderId,
                'momo_signature' => $momoSignature,
                'my_signature' => $mySignature,
            ]);
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
