<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Abstractions\Repositories\RoleRepository;
use App\Data\RoleClaimData;

class UpdateRoleClaimAction
{
    public function __construct(
        private RoleRepository $roleRepository
    ) {
    }
    public function handle(int $roleId, RoleClaimData ...$claims): void
    {
        $role = $this->roleRepository->getById($roleId);
        if($role->name === 'Admin'){
            throw new \Exception('Không thể cập nhật quyền cho vai trò Admin');
        }
        $this->roleRepository->updateRoleClaims(...$claims);
    }
}
