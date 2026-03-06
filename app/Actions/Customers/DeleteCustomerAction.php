<?php

declare(strict_types=1);  

namespace App\Actions\Customers;

use App\Abstractions\Repositories\CustomerRepository;

class DeleteCustomerAction
{
    public function __construct(
        private CustomerRepository $customerRepository
    ) {
    }
    public function handle(int $id): void
    {
        $customer = $this->customerRepository->findById($id);
        if (!$customer) {
            throw new \Exception('Khách hàng không tồn tại');
        }

        if($customer->bookings()->exists()){
            throw new \Exception('Không thể xóa khách hàng có đặt phòng');
        }
        $this->customerRepository->delete($customer);
    }
}