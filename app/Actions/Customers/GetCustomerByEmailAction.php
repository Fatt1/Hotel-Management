<?php

namespace App\Actions\Customers;

use App\Models\Customer;

class GetCustomerByEmailAction
{
    public function handle(string $email): ?Customer
    {
        return Customer::where('email', $email)->first();
    }
}