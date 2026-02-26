<?php

namespace App\Repositories;

use App\Abstractions\Repositories\BookingRepository;
use App\Models\Booking;


class EloquentBookingRepository implements BookingRepository
{
    
    public function getBookingById(int $bookingId)
    {
        return Booking::find($bookingId);
    }

    public function save(Booking $booking): bool
    {
        return $booking->save();
    }

    public function deleteBooking(int $bookingId)
    {
        $booking = $this->getBookingById($bookingId);
        if ($booking) {
            $booking->delete();
        }
        
    }
}
