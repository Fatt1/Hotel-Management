<?php

namespace App\Abstractions\Repositories;

use App\Models\Staff;
use Illuminate\Pagination\LengthAwarePaginator;

interface IStaffRepository
{
    public function findById(int $id): ?Staff;

    public function save(Staff $staff): bool;

    public function delete(Staff $staff): bool;

    public function all(int $pageSize = 10, int $pageNumber = 1, ?string $search = null, ?int $roleId = null): LengthAwarePaginator;
}
