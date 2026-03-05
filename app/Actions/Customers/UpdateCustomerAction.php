<?php

declare(strict_types=1);

namespace App\Actions\Customers;

use App\Abstractions\Repositories\CustomerRepository;
use App\Data\CustomerData;
use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;

class UpdateCustomerAction
{
    public function __construct(
        private CustomerRepository $customerRepository
    ) {
    }
    public function handle(int $id, CustomerData $data): Customer
    {
        $customer = $this->customerRepository->findById($id);
        if (!$customer) {
            throw new \Exception('Khách hàng không tồn tại');
        }
        $customer->first_name = $data->first_name;
        $customer->last_name = $data->last_name;
        $customer->phone_number = $data->phone_number;
        $customer->country = $data->country;
        $customer->email = $data->email;
        if (!empty($data->password)) {
            $customer->password = bcrypt($data->password);
        }
        $this->customerRepository->save($customer);
        return $customer;
    }
}

