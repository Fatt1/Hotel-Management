<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'first_name'   => 'Nguyễn',
                'last_name'    => 'Văn An',
                'phone_number' => '0901234567',
                'country'      => 'Vietnam',
                'email'        => 'nguyenvanan@example.com',
            ],
            [
                'first_name'   => 'Trần',
                'last_name'    => 'Thị Bình',
                'phone_number' => '0912345678',
                'country'      => 'Vietnam',
                'email'        => 'tranthibinh@example.com',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
