<?php

declare(strict_types=1);

namespace App\Actions\Customers;

use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;

class GetCustomerByIdAction
{
    public function handle(int $id): Customer
    {
        $customer = Customer::with(['bookings' => function($q){
            $q-> orderBy('booking_date', 'desc');
        },'bookings.bookingDetails'])->find($id);
        if (!$customer) {
            throw new \Exception('Khách hàng không tồn tại');
        }
        return $customer;
    }
}