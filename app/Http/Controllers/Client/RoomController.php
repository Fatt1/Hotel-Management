<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\BookingDetail;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Hiển thị danh sách loại phòng với bộ lọc tìm kiếm.
     */
    public function index(Request $request)
    {
        // --- Parse search params ---
        $checkIn    = $request->input('check_in',  now()->format('Y-m-d'));
        $checkOut   = $request->input('check_out', now()->addDays(3)->format('Y-m-d'));
        $adults     = max(1, (int) $request->input('adults', 2));
        $children   = max(0, (int) $request->input('children', 0));
        $roomsCount = max(1, (int) $request->input('rooms_count', 1));

        // Validate & sanitise dates
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

        // --- Room IDs bị trùng lịch (chỉ tính booking đang active: 'Đã đặt', 'Đang ở') ---
        $bookedRoomIds = BookingDetail::join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
            ->whereIn('bookings.status', ['Đã đặt', 'Đang ở'])
            ->where('booking_details.checkin_date', '<', $checkOut)
            ->where('booking_details.checkout_date', '>', $checkIn)
            ->pluck('booking_details.room_id')
            ->toArray();

        // --- Load room types, lọc theo sức chứa ---
        $roomTypes = RoomType::where('is_active', true)
            ->where('adult_quantity', '>=', $adults)
            ->with([
                'images'    => fn ($q) => $q->orderBy('order'),
                'amenities' => fn ($q) => $q->orderBy('name'),
            ])
            ->get();

        // Gắn available_count vào từng room type
        foreach ($roomTypes as $rt) {
            $rt->available_count = $rt->rooms()
                ->where('status', 'ready')
                ->whereNotIn('id', $bookedRoomIds)
                ->count();
        }

        return view('client.rooms.index', compact(
            'roomTypes', 'checkIn', 'checkOut',
            'adults', 'children', 'roomsCount', 'nights'
        ));
    }

    /**
     * Hiển thị chi tiết một loại phòng (show page).
     */
    public function show(int $id)
    {
        $roomType = RoomType::with(['images', 'amenities', 'rooms'])
            ->findOrFail($id);

        return view('client.rooms.show', compact('roomType'));
    }
}
