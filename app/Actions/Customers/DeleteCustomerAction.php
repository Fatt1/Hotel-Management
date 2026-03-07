<?php

declare(strict_types=1);  

namespace App\Actions\Customers;

use App\Models\Customer;

class DeleteCustomerAction
{
    public function handle(int $id): void
    {
        $customer = Customer::findOrFail($id);

        if ($customer->bookings()->exists()) {
            throw new \Exception('Không thể xóa khách hàng có đặt phòng');
        }
        $customer->delete();
    }
}