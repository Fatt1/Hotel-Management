<?php

declare(strict_types=1);

namespace App\Actions\Customers;

use App\Data\CustomerData;
use App\Models\Customer;

class UpdateCustomerAction
{
    public function handle(int $id, CustomerData $data): Customer
    {
        $customer = Customer::findOrFail($id);
        $customer->first_name = $data->first_name;
        $customer->last_name = $data->last_name;
        $customer->phone_number = $data->phone_number;
        $customer->country = $data->country;
        $customer->email = $data->email;
        $customer->save();
        return $customer;
    }
}

