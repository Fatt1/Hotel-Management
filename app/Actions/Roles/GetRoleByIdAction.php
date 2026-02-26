<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Abstractions\Repositories\RoleRepository;
use App\Models\Role;

class GetRoleByIdAction
{
    public function __construct(
        private RoleRepository $roleRepository
    ) {
    }
    public function handle(int $id, bool $withClaims = false): Role
    {
        $role = $this->roleRepository->findById($id, $withClaims);
        if (!$role) {
            throw new \Exception('Role not found');
        }
        return $role;
    }
}
