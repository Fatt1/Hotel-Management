<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Models\Role;
use App\Data\RoleData;

class UpdateRoleAction
{
    public function handle(int $id, RoleData $roleData): Role
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'Admin') {
            throw new \Exception('Không thể sửa vai trò Admin');
        }

        $role->name = $roleData->name;
        $role->save();

        return $role;
    }
}
