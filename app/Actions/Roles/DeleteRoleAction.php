<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Abstractions\Repositories\RoleRepository;

class DeleteRoleAction
{
    public function __construct(
      private RoleRepository $roleRepository
    ) {
    }
    public function handle(int $id): void
    {
        $role = $this->roleRepository->findById($id);
        if (!$role) {
            throw new \Exception('Role not found');
        }
        if($role->staff()->count() > 0) {
            throw new \Exception('Cannot delete role with assigned staff');
        }
        if($role->name === 'Admin') {
            throw new \Exception('Cannot delete Admin role');
        }
        $this->roleRepository->delete($role);
    }
}
