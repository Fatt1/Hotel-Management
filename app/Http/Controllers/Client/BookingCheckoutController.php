<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\BookingSuccessMail;
use App\Models\RoomType;
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
                        'name'       => $rt->name,
                        'image_url'  => $rt->images->first()?->image_url ?? '',
                        'width'      => $rt->width ?? 0,
                        'qty'        => $qty,
                        'line_total' => $price * $qty * $nights,
                    ];
                    $subtotal += $price * $qty * $nights;
                }
            }
        }

        $bookingData = [
            'check_in'    => $checkIn,
            'check_out'   => $checkOut,
            'adults'      => $adults,
            'children'    => $children,
            'nights'      => $nights,
            'rooms'       => $selectedRooms,
            'subtotal'    => $subtotal,
            'guest_name'  => trim($request->input('first_name', '') . ' ' . $request->input('last_name', '')),
            'guest_email' => $request->input('email_verify', ''),
            'payment'     => $request->input('payment_method', 'credit'),
            'confirmed_at'=> now()->format('d/m/Y H:i'),
        ];

        // Store in session
        session([
            'booking_ref'  => $bookingRef,
            'booking_data' => $bookingData,
        ]);

        // Queue confirmation email asynchronously (do not block response)
        if (filter_var($bookingData['guest_email'], FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($bookingData['guest_email'])->queue(new BookingSuccessMail($bookingRef, $bookingData));
            } catch (\Throwable $e) {
                Log::warning('Queue booking confirmation email failed', [
                    'booking_ref' => $bookingRef,
                    'guest_email' => $bookingData['guest_email'],
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('client.booking.confirmation');
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
}
