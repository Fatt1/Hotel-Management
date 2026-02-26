<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Abstractions\Repositories\RoleRepository;
use App\Data\RoleClaimData;
use App\Models\RoleClaim;

class UpdateRoleClaimAction
{
    public function __construct(
        private RoleRepository $roleRepository
    ) {
    }

    public function handle(int $roleId, RoleClaimData ...$claims): void
    {
        $role = $this->roleRepository->findById($roleId);
        if (!$role) {
            throw new \Exception('Role not found');
        }
        if ($role->name === 'Admin') {
            throw new \Exception('Không thể cập nhật quyền cho vai trò Admin');
        }

        foreach ($claims as $claimData) {
            $roleClaim = $this->roleRepository->findRoleClaimBy($roleId, $claimData->claim_name);

            if (!$roleClaim) {
                $roleClaim = new RoleClaim();
                $roleClaim->role_id = $roleId;
                $roleClaim->claim_name = $claimData->claim_name;
            }

            $roleClaim->claim_value = $claimData->claim_value;
            $this->roleRepository->saveRoleClaim($roleClaim);
        }
    }
}
