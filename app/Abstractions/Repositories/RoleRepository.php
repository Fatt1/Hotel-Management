<?php

declare(strict_types=1);

namespace App\Abstractions\Repositories;

use App\Models\Role;
use App\Models\RoleClaim;

interface RoleRepository
{
    public function findById(int $id, bool $withClaims = false): ?Role;

    public function save(Role $role): bool;

    public function delete(Role $role): bool;

    public function findRoleClaimBy(int $roleId, string $claimName): ?RoleClaim;

    public function saveRoleClaim(RoleClaim $roleClaim): bool;
}
