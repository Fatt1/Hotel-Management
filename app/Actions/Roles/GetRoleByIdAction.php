<?php

declare(strict_types=1);

namespace App\Actions\Roles;

use App\Models\Role;

class GetRoleByIdAction
{
    public function handle(int $id, bool $withClaims = false): Role
    {
        $query = $withClaims ? Role::with('claims') : Role::query();
        return $query->findOrFail($id);
    }
}
