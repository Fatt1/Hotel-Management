<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Abstractions\Repositories\RoleRepository;
use App\Models\Role;
use App\Models\RoleClaim;

class EloquentRoleRepository implements RoleRepository
{
    public function findById(int $id, bool $withClaims = false): ?Role
    {
        $query = Role::query();
        if ($withClaims) {
            $query = $query->with('claims');
        }
        return $query->find($id);
    }

    public function save(Role $role): bool
    {
        return $role->save();
    }

    public function delete(Role $role): bool
    {
        return $role->delete();
    }

    public function findRoleClaimBy(int $roleId, string $claimName): ?RoleClaim
    {
        return RoleClaim::where('role_id', $roleId)
            ->where('claim_name', $claimName)
            ->first();
    }

    public function saveRoleClaim(RoleClaim $roleClaim): bool
    {
        return $roleClaim->save();
    }
}

