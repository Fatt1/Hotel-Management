<?php

declare(strict_types=1);

namespace App\Actions\RoomTypes;

use App\Models\BookingDetail;
use App\Models\RoomType;
use Illuminate\Support\Collection;

class SearchAvailableRoomTypesAction
{
    /**
     * Tìm kiếm các loại phòng còn trống theo sức chứa và khoảng thời gian.
     */
    public function execute(string $checkIn, string $checkOut, int $adults): Collection
    {
        // --- Lấy ID của các phòng đã được đặt/đang ở trong khoảng thời gian này ---
        $bookedRoomIds = BookingDetail::join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
            ->whereIn('bookings.status', ['Đã đặt', 'Đang ở'])
            ->where('booking_details.checkin_date', '<', $checkOut)
            ->where('booking_details.checkout_date', '>', $checkIn)
            ->pluck('booking_details.room_id')
            ->toArray();

        // --- Load danh sách loại phòng phù hợp sức chứa ---
        $roomTypes = RoomType::where('is_active', true)
            ->where('adult_quantity', '>=', $adults)
            ->with([
                'images'    => fn ($q) => $q->orderBy('order'),
                'amenities' => fn ($q) => $q->orderBy('name'),
            ])
            ->get();

        // --- Gắn số lượng phòng trống (trừ các phòng bị trùng lịch và chưa sẵn sàng) ---
        foreach ($roomTypes as $rt) {
            /** @var \App\Models\RoomType $rt */
            $rt->available_count = $rt->rooms()
                ->where('status', 'ready')
                ->whereNotIn('id', $bookedRoomIds)
                ->count();
        }

        return $roomTypes;
    }
}
