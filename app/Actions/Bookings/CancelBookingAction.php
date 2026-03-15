<?php

namespace App\Actions\Bookings;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class CancelBookingAction
{
    public function execute(int $bookingId): void
    {
        DB::transaction(function () use ($bookingId) {
            $booking = Booking::findOrFail($bookingId);

            if ($booking->status === 'Hủy') {
                throw new \Exception("Booking đã được hủy trước đó.");
            }
            else if ($booking->status === "Đã đặt") {
                $booking->status = 'Hủy';
                $booking->save();
            }
            else 
                throw new \Exception('Booking đã xác nhận không thể hủy');
        });
    }
}
