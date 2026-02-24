<?php

declare(strict_types=1);

namespace App\Actions;

use App\Abstractions\Repositories\RoleRepository;
use App\Models\Role;

class GetRoleByIdAction
{
    public function __construct(
        private RoleRepository $roleRepository
    ) {
    }
    public function handle(int $id)
    {
        $role = $this->roleRepository->getById($id);
        if (!$role) {
            throw new \Exception('Role not found');
        }
        return $role;
    }
}
