<?php

namespace App\Actions\Bookings;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class CheckinBookingAction
{
    public function execute(int $bookingId): void
    {
        DB::transaction(function () use ($bookingId) {
            $booking = Booking::findOrFail($bookingId);
            
            // Chỉ cho phép check-in nếu booking đang ở trạng thái "Đã đặt"
            if ($booking->status !== 'Đã đặt') {
                throw new \Exception("Chỉ có thể check-in cho booking đang ở trạng thái 'Đã đặt'.");
            }
            
            $booking->status = 'Đang ở';
            $booking->save();
        });
    }
}