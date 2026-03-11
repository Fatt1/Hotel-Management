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
            'customer:id,first_name,last_name,email,country,phone_number',
            'payments.staff:id,first_name,last_name',
            'bookingDetails.room.roomType',
            'bookingDetails.room.floor',
            'bookingDetails.serviceUsages.service.group',
        ])->findOrFail($bookingId);
    }
}
