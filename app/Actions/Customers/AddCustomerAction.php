<?php

declare(strict_types=1);

namespace App\Actions\Customers;

use App\Data\CustomerData;
use App\Models\Customer;

class AddCustomerAction
{
    public function handle(CustomerData $data): Customer
    {
        $customer = new Customer();
        $customer->first_name = $data->first_name;
        $customer->last_name = $data->last_name;
        $customer->phone_number = $data->phone_number;
        $customer->country = $data->country;
        $customer->email = $data->email;
        $customer->password = bcrypt(uniqid());
        $customer->save();
        return $customer;
    }
}