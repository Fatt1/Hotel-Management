<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Abstractions\Repositories\CustomerRepository;
use App\Models\Customer;

class EloquentCustomerRepository implements CustomerRepository
{
    public function findById(int $id): ?Customer
    {
        return Customer::find($id);
    }

    public function save(Customer $customer): bool
    {
        return $customer->save();
    }

    public function delete(Customer $customer): bool
    {
        return $customer->delete();
    }
}