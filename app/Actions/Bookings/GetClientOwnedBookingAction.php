<?php

namespace App\Actions\Bookings;

use App\Models\Booking;

class GetClientOwnedBookingAction
{
    public function execute(int $customerId, int $bookingId, array $relations = []): Booking
    {
        $query = Booking::query()
            ->where('customer_id', $customerId)
            ->where('id', $bookingId);

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->firstOrFail();
    }
}
