<?php

declare(strict_types=1);

namespace App\Abstractions\Repositories;

use App\Data\RoleData;
use App\Models\Role;
use App\Models\RoleClaim;
use App\Data\RoleClaimData;

interface RoleRepository
{
    public function add(RoleData $data): Role;
    public function update(int $id, RoleData $data): Role;
    public function delete(Role $role): bool;
    public function getById(int $id, bool $withClaims = false): ?Role;

    public function addRoleClaims(int $roleId, RoleClaim ...$claims): void;
    public function updateRoleClaims(RoleClaimData ...$claims): void;
}
