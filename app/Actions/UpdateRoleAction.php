<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Role;
use App\Abstractions\Repositories\RoleRepository;
use App\Data\RoleData;

class UpdateRoleAction
{
    public function __construct(
        private RoleRepository $roleRepository
    ) {
    }

    public function handle(int $id, RoleData $roleData): Role
    {
        $role = $this->roleRepository->getById($id);
        if (!$role) {
            throw new \Exception('Role not found');
        }
        if($role->name === 'Admin') {
            throw new \Exception('Không thể sửa vai trò Admin');
        }
        return $this->roleRepository->update($id, $roleData);
    }
}
