<?php

namespace App\Actions\Customers;

use App\Models\Customer;

class GetCustomerByEmailAction
{
    public function handle(string $email): Customer
    {
        $customer = Customer::where('email', $email)->first();

        if (!$customer) {
            throw new \Exception('Khách hàng không tồn tại');
        }

        return $customer;
    }
}