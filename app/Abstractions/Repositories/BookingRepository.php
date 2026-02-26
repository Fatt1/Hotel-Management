<?php

namespace App\Abstractions\Repositories;

use App\Models\Booking;

interface BookingRepository
{
    public function getBookingById(int $bookingId);
    public function save(Booking $booking): bool;
      public function deleteBooking(int $bookingId);
}