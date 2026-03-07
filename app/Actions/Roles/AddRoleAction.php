<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Data\RoleData;
use App\Models\Role;

class AddRoleAction
{
    public function handle(RoleData $roleData): Role
    {
        $role = new Role();
        $role->name = $roleData->name;
        $role->save();

        return $role;
    }
}
