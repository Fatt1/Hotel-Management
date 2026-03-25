<?php

namespace App\Actions\Customers;

use App\Models\Customer;

class GetCustomerByEmailAction
{
    public function handle(string $email): Customer | null
    {
        $customer = Customer::where('email', $email)->first();

        if (!$customer) {
            return null;
        }

        return $customer;
    }
}