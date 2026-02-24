<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Role;
use App\Abstractions\Repositories\RoleRepository;
use App\Data\RoleData;

class AddRoleAction
{
    public function __construct(
        private RoleRepository $roleRepository
    ) {
    }

    public function handle(RoleData $roleData) {

        $newRole = new Role($roleData->toArray());
        $role = $this->roleRepository->add($newRole);
        return $role;
    }
}
