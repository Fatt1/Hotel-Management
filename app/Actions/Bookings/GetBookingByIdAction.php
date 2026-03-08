<?php

namespace App\Actions\Bookings;

use App\Models\Booking;

class GetBookingByIdAction
{
    /**
     * Lấy thông tin booking theo ID với đầy đủ relationships
     * 
     * @param int $bookingId
     * @return Booking
     */
    public function execute(int $bookingId): Booking
    {
        return Booking::with([
            'customer:id,first_name,last_name,email,phone_number,country',
            'staff:id,first_name,last_name',
            'bookingDetails' => function ($query) {
                $query->with([
                    'room:id,name,status,room_type_id,floor_id',
                    'room.roomType:id,name,code',
                    'room.floor:id,name',
                    'serviceUsages' => function ($q) {
                        $q->with('service:id,name,unit_price');
                    }
                ]);
            }
        ])->findOrFail($bookingId);
    }
}
