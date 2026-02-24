<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Abstractions\Repositories\RoleRepository;
use App\Data\RoleData;
use App\Models\Role;
use App\Models\RoleClaim;

class EloquentRoleRepository implements RoleRepository
{
    public function add(Role $data): Role
    {
        $role = Role::create([
            'name' => $data->name,
        ]);
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

    public function getById(int $id): ?Role
    {
        return Role::find($id);
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

    public function updateRoleClaims(RoleClaim ...$claims): void
    {
        foreach ($claims as $claim) {
          $claim = RoleClaim::find($claim->id);
          if($claim) {
              $claim->update([
                  'claim_type' => $claim->claim_type,
                  'claim_value' => $claim->claim_value,
              ]);
          }
        }
    }
}
