<?php

namespace App\Actions\Rooms;

use App\Enums\RoomStatus;
use App\Models\Room;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class GetAvailableRoomsAction
{
    /**
     * Lấy danh sách phòng còn trống trong khoảng thời gian check-in/check-out.
     *
     * Phòng được coi là bận nếu:
     * - Có booking_detail trùng lịch (checkin_date < $checkout_date AND checkout_date > $checkin_date)
     * - Thuộc booking đang active (status: 'Đã đặt' hoặc 'Đang ở')
     */
    public function handle(string $checkinDate, string $checkoutDate, ?int $roomTypeId = null, ?int $floorId = null): Collection
    {
        $bookedRoomIds = DB::table('booking_details')
            ->join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
            ->whereIn('bookings.status', ['Đã đặt', 'Đang ở'])
            ->where('booking_details.checkin_date', '<', $checkoutDate)
            ->where('booking_details.checkout_date', '>', $checkinDate)
            ->pluck('booking_details.room_id');

        return Room::where('status', RoomStatus::READY)
            ->whereNotIn('id', $bookedRoomIds)
            ->when($roomTypeId, function ($query) use ($roomTypeId) {
                $query->where('room_type_id', $roomTypeId);
            })
            ->when($floorId, function ($query) use ($floorId) {
                $query->where('floor_id', $floorId);
            })
            ->with(['roomType', 'floor'])
            ->get();
    }
}