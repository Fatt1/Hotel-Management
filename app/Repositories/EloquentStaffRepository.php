<?php

namespace App\Repositories;

use App\Abstractions\Repositories\IStaffRepository;
use App\Models\Staff;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentStaffRepository implements IStaffRepository
{
    public function findById(int $id): ?Staff
    {
        return Staff::find($id);
    }

    public function save(Staff $staff): bool
    {
        return $staff->save();
    }

    public function delete(Staff $staff): bool
    {
        return $staff->delete();
    }

    public function all(int $pageSize = 10, int $pageNumber = 1, ?string $search = null, ?int $roleId = null): LengthAwarePaginator
    {
        return Staff::query()
            ->with('role')
            ->when($search, function ($query, $search) {
                return $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($roleId, function ($query, $roleId) {
                return $query->where('role_id', $roleId);
            })
            ->paginate($pageSize, ['*'], 'page', $pageNumber);
    }
}
