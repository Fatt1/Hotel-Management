<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Abstractions\Repositories\RoleRepository;
use App\Data\RoleData;
use App\Models\Role;
use App\Models\RoleClaim;
use App\Data\RoleClaimData;

class EloquentRoleRepository implements RoleRepository
{
    public function add(RoleData $data): Role
    {
        $role = Role::create($data->toArray());
        return $role;
    }

    public function update(int $id, RoleData $data): Role
    {
        $role = Role::findOrFail($id);
        $role->update([
            'name' => $data->name,
        ]);
        return $role;
    }

    public function delete(Role $role): bool
    {
        if ($role) {
            return $role->delete();
        }
        return false;

    }

    public function getById(int $id, bool $withClaims = false): ?Role
    {
        $query = Role::query();
        if ($withClaims) {
           $query = $query->with('claims');
        }
        return $query->find($id);
    }

    public function addRoleClaims(int $roleId, RoleClaim ...$claims): void
    {
        RoleClaim::insert(array_map(function (RoleClaim $claim) use ($roleId) {
            return [
                'role_id' => $roleId,
                'claim_type' => $claim->claim_type,
                'claim_value' => $claim->claim_value,
            ];
        }, $claims));
    }

    public function updateRoleClaims(RoleClaimData ...$claims): void
    {
        foreach ($claims as $claimData){
            RoleClaim::updateOrInsert(
                // Điều kiện để xác định bản ghi cần cập nhật (ở đây là dựa trên id) --- IGNORE ---
                ['id'=> $claimData->id],
                [
                    'role_id' => $claimData->role_id,
                    'claim_type' => $claimData->claim_type,
                    'claim_value' => $claimData->claim_value,
                ]
            );
        };
    }
}
