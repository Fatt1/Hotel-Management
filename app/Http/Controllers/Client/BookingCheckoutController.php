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
    public function checkout(Request $request, \App\Actions\Bookings\CalculateCheckoutCartAction $cartAction)
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
        $nights   = max(1, (int) $checkInDate->copy()->startOfDay()->diffInDays($checkOutDate->copy()->startOfDay()));

        // --- Các phòng đã chọn (qty_{id} => số lượng) ---
        // Rooms page POSTs: qty_1=2, qty_5=1, ... kèm check_in, check_out, adults, children, rooms_count
        $cart = $cartAction->execute($request->all(), $nights);
        $selectedRooms = $cart['selectedRooms'];
        $subtotal = $cart['subtotal'];

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

        // Lấy thông tin khách hàng đang đăng nhập nếu có
        $customer = \Illuminate\Support\Facades\Auth::guard('customer')->user();

        return view('client.booking.checkout', compact(
            'selectedRooms', 'subtotal',
            'checkIn', 'checkOut', 'checkInDate', 'checkOutDate',
            'adults', 'children', 'roomsCount', 'nights', 'customer'
        ));
    }

    /**
     * Hiển thị trang chọn phương thức thanh toán (Step 3/3).
     * Nhận dữ liệu từ form thông tin khách (checkout page).
     */
    public function payment(Request $request, \App\Actions\Bookings\CalculateCheckoutCartAction $cartAction)
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
        $nights   = max(1, (int) $checkInDate->copy()->startOfDay()->diffInDays($checkOutDate->copy()->startOfDay()));

        // Guest info passed through from step 2
        $guestInfo = [
            'first_name' => $payload['first_name'] ?? '',
            'last_name'  => $payload['last_name'] ?? '',
            'country'    => $payload['country'] ?? 'VN',
            'phone_code' => $payload['phone_code'] ?? '+84',
            'phone'      => $payload['phone'] ?? '',
        ];

        // Selected rooms
        $cart = $cartAction->execute($payload, $nights);
        $selectedRooms = $cart['selectedRooms'];
        $subtotal = $cart['subtotal'];

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
    public function confirm(\App\Data\CreateBookingOnlineData $request, \App\Actions\Bookings\CreateBookingOnlineAction $action)
    {
        try {
            $booking = $action->execute($request);

            return response()->json([
                'success' => true,
                'message' => 'Đặt phòng thành công.',
                'booking_id' => $booking->id
            ]);
        } catch (\Exception $e) {
            Log::error('DB Booking Create Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Return URL sau khi quẹt mã QR MoMo chuyển hướng user về
     */
    public function momoReturn(Request $request, \App\Actions\Bookings\GetBookingConfirmationDataAction $confirmationDataAction)
    {
        $resultCode = (string) $request->query('resultCode', '');
        $orderId    = (string) $request->query('orderId', '');
        $bookingId  = (int) explode('_', $orderId)[0];

        Log::info('MoMo return payload', [
            'query' => $request->query(),
        ]);

        $booking = Booking::with(['customer', 'bookingDetails.room.roomType.images', 'payments'])->find($bookingId);
        
        if (!$booking) {
             return redirect()->route('client.rooms.index')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $isPaid = (bool) ($booking->payments?->contains(function ($payment) {
            return $payment->payment_method === 'momo';
        }) ?? false);

        $bookingDataArray = $confirmationDataAction->execute($booking);

        $success = $resultCode === '0';
        $message = $success
            ? ($isPaid
                ? ('Thanh toán MoMo thành công! Mã đơn hàng của bạn là: ' . $bookingId)
                : ('Giao dịch thành công trên MoMo, đang chờ xác nhận thanh toán từ hệ thống (IPN).'))
            : ('Giao dịch MoMo thất bại hoặc đã bị huỷ (Mã Lỗi: ' . $resultCode . ').');

        return view('client.booking.confirmation', [
            'bookingRef'   => $bookingDataArray['booking_ref'],
            'bookingData'  => $bookingDataArray,
            'checkInDate'  => $bookingDataArray['checkInDate'],
            'checkOutDate' => $bookingDataArray['checkOutDate'],
            'success'      => $success,
            'isPaid'       => $isPaid,
            'message'      => $message,
        ]);
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
    public function confirmation(Request $request, \App\Actions\Bookings\GetBookingConfirmationDataAction $confirmationDataAction)
    {
        $bookingRef  = session('booking_ref');
        $bookingData = session('booking_data');

        if (! $bookingRef || ! $bookingData) {
            // Check if there is a booking_id provided via Axios redirect
            if ($request->has('booking_id')) {
                $booking = Booking::with(['customer', 'bookingDetails.room.roomType.images', 'payments'])->find($request->input('booking_id'));
                if ($booking) {
                    $bookingDataArray = $confirmationDataAction->execute($booking);

                    return view('client.booking.confirmation', [
                        'bookingRef'   => $bookingDataArray['booking_ref'],
                        'bookingData'  => $bookingDataArray,
                        'checkInDate'  => $bookingDataArray['checkInDate'],
                        'checkOutDate' => $bookingDataArray['checkOutDate'],
                        'success'      => true,
                        'isPaid'       => true,
                        'message'      => 'Đặt phòng và thanh toán thành công!',
                    ]);
                }
            }
            return redirect()->route('client.rooms.index');
        }

        try {
            $checkInDate  = Carbon::parse($bookingData['check_in']);
            $checkOutDate = Carbon::parse($bookingData['check_out']);
        } catch (\Throwable) {
            $checkInDate  = now();
            $checkOutDate = now()->addDays(3);
        }

        $success = session('success') ? true : false;
        if (!isset($isPaid)) $isPaid = true;
        if (!isset($message)) $message = 'Đặt phòng thành công.';

        return view('client.booking.confirmation', compact('bookingRef', 'bookingData', 'checkInDate', 'checkOutDate', 'success', 'isPaid', 'message'));
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
                    'phone'      => $customer->phone_number,
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
