<?php

namespace App\Actions\Customers;

use App\Models\Customer;
use Illuminate\Support\Facades\Validator;

class GetCustomerByEmailAction
{
    public function handle(?string $email): ?Customer
    {
        Validator::make(['email' => $email], [
            'email' => 'required|email',
        ], [
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không hợp lệ.',
        ])->validate();

        return Customer::where('email', $email)->first();
    }
}