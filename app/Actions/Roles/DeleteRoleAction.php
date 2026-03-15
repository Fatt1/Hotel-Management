<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Models\Role;

class DeleteRoleAction
{
    public function handle(int $id): void
    {
        $role = Role::findOrFail($id);

        if ($role->staff()->count() > 0) {
            throw new \Exception('Cannot delete role with assigned staff');
        }
        if ($role->name === 'Admin') {
            throw new \Exception('Cannot delete Admin role');
        }
        $role->delete();
    }
}
