<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Data\RoleClaimData;
use App\Models\Role;
use App\Models\RoleClaim;
use Illuminate\Support\Facades\Cache;

class UpdateRoleClaimAction
{
    public function handle(int $roleId, RoleClaimData ...$claims): void
    {
        $role = Role::findOrFail($roleId);

        if ($role->name === 'Admin') {
            throw new \Exception('Không thể cập nhật quyền cho vai trò Admin');
        }

        foreach ($claims as $claimData) {
            $roleClaim = RoleClaim::where('role_id', $roleId)
                ->where('claim_name', $claimData->claim_name)
                ->first();

            if (!$roleClaim) {
                $roleClaim = new RoleClaim();
                $roleClaim->role_id = $roleId;
                $roleClaim->claim_name = $claimData->claim_name;
            }

            $roleClaim->claim_value = $claimData->claim_value;
            $roleClaim->save();
        }

        // Refresh cache sau khi cập nhật
        $cachedClaims = RoleClaim::where('role_id', $roleId)
            ->get()
            ->pluck('claim_value', 'claim_name')
            ->map(fn ($val) => (int) $val)
            ->all();

        Cache::forever("role_claims_{$roleId}", $cachedClaims);
    }
}
