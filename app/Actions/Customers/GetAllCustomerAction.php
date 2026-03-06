<?php

declare(strict_types=1);

namespace App\Actions\Customers;

use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;

class GetAllCustomerAction
{
    public function handle(
        int     $pageSize = 10,
        int     $pageNumber = 1,
        ?string $search = null,
        ?string $country = null,
        string  $sortBy = 'id',
        string  $sortDir = 'desc'
    ): LengthAwarePaginator {
        $query = Customer::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone_number', 'like', '%' . $search . '%');
            });
        }

        if ($country) {
            $query->where('country', $country);
        }

        // Chỉ cho phép sort theo các cột hợp lệ
        $allowedSortBy = ['id', 'first_name', 'last_name', 'email', 'country'];
        $sortBy  = in_array($sortBy, $allowedSortBy) ? $sortBy : 'id';
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sortBy, $sortDir)
                     ->paginate($pageSize, ['*'], 'page', $pageNumber);
    }
}