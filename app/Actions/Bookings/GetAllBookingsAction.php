<?php

namespace App\Actions\Bookings;

use App\Models\Booking;

class GetAllBookingsAction
{

    public function handle(int $page_number, int $page_size, $search = null, $from_date = null, $to_date = null, $status = null)
    {
        $query = Booking::query()->with([
            'customer:id,first_name,last_name,email,phone_number',
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($q2) use ($q, $search) {
                   $q2->where('first_name', 'like', "%$search%")
                      ->orWhere('last_name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%");

                });
            });
        }

        if ($from_date) {
            $query->whereDate('booking_date', '>=', $from_date);
        }

        if ($to_date) {
            $query->whereDate('booking_date', '<=', $to_date);
        }
        if ($status) {
            $query->where('status', $status);
        }
        $query = $query->orderBy('booking_date', 'desc');

        return $query->paginate($page_size, ['*'], 'page', $page_number);
    }
}
