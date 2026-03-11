<?php

namespace App\Actions\Bookings;

use App\Models\Booking;
use App\Models\Payment;

class CheckoutBookingAction
{
    public function execute(int $bookingId, float $paymentAmount = 0, string $paymentMethod = 'cash'): void
    {
        $booking = Booking::with('bookingDetails.room')->findOrFail($bookingId);

        if (!in_array($booking->status, ['Đang ở', 'Quá giờ'])) {
            throw new \Exception("Chỉ có thể checkout cho booking đang ở trạng thái 'Đang ở' hoặc 'Quá giờ'.");
        }

        // Mark booking as checked out
        $booking->status = 'Đã trả phòng';
        $booking->save();

        // Mark all rooms as dirty (need cleaning)
        foreach ($booking->bookingDetails as $detail) {
            $detail->room->update(['status' => 'dirty']);
        }
    }
}
