<?php

declare(strict_types=1);

namespace App\Actions\Customers;

use App\Abstractions\Repositories\CustomerRepository;
use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Data\CustomerData;

class AddCustomerAction
{
    public function __construct(
        private CustomerRepository $customerRepository
    ) {
    }
    public function handle(CustomerData $data): Customer
    {
        $customer = new Customer();
        $customer->first_name = $data->first_name;
        $customer->last_name = $data->last_name;
        $customer->phone_number = $data->phone_number;
        $customer->country = $data->country;
        $customer->email = $data->email;
        $customer->password = bcrypt($data->password);
        $this->customerRepository->save($customer);
        return $customer;
    }
}