<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Abstractions\Repositories\RoleRepository;
use App\Data\RoleData;
use App\Models\Role;

class AddRoleAction
{
    public function __construct(
        private RoleRepository $roleRepository
    ) {
    }

    public function handle(RoleData $roleData): Role
    {
        $role = new Role();
        $role->name = $roleData->name;

        $this->roleRepository->save($role);

        return $role;
    }
}
