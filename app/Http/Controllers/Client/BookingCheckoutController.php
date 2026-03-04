<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Carbon\Carbon;
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
}
