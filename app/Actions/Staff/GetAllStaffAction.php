<?php

namespace App\Actions\Staff;

use App\Models\Staff;
use Illuminate\Pagination\LengthAwarePaginator;

class GetAllStaffAction
{
    /**
     * @param int 
     * @param int 
     * @param string|null 
     * @param int|null 
     * @param string|null 
     */
    public function handle(
        int $pageSize = 10,
        int $pageNumber = 1,
        ?string $search = null,
        ?int $roleId = null,
        ?string $sort = null
    ): LengthAwarePaginator {
        $query = Staff::query()->with('role');

        // Filter: Tìm kiếm
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Filter: Theo role
        if ($roleId) {
            $query->where('role_id', $roleId);
        }

        // Sort: Sắp xếp
        if ($sort) {
            [$sortField, $sortDirection] = explode(':', $sort);
            $sortDirection = strtoupper($sortDirection) === 'DESC' ? 'DESC' : 'ASC';

            
            if ($sortField === 'role') {
                $query->leftJoin('roles', 'staffs.role_id', '=', 'roles.id')
                    ->orderBy('roles.name', $sortDirection)
                    ->select('staffs.*');
            } else {
                
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            
            $query->orderBy('staffs.id', 'DESC');
        }

        // Pagination: Phân trang
        return $query->paginate($pageSize, ['staffs.*'], 'page', $pageNumber);
    }
}
