<?php

namespace App\Actions\Bookings;

use App\Abstractions\Repositories\BookingRepository;
use App\Models\Booking;

class GetAllBookingsAction {
    
    public function handle(int $page_number, int $page_size, $search = null, $from_date = null, $to_date = null, $status = null)
    {
         $query = Booking::query()->with([
    'customer:id,first_name,last_name,email,phone_number', 
]);

        if ($search) {
            $query->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('id', 'like', "%$search%");
        }

        if ($from_date) {
            $query->whereDate('booking_date', '>=', $from_date);
        }

        if ($to_date) {
            $query->whereDate('booking_date', '<=', $to_date);
        }
        if ($status){
            $query->where('status', $status);
        }

        return $query->paginate($page_size, ['*'], 'page', $page_number);

    }
}