<?php
declare(strict_types=1);

namespace App\Abstractions\Repositories;

use App\Models\Customer;

interface CustomerRepository
{
    public function findById(int $id): ?Customer;

    public function save(Customer $customer): bool;

    public function delete(Customer $customer): bool;
}