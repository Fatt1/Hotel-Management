<?php

declare(strict_types=1);

namespace App\Abstractions\Repositories;

use App\Data\RoleData;
use App\Models\Role;
use App\Models\RoleClaim;


interface RoleRepository
{
    public function add(Role $data): Role;
    public function update(int $id, RoleData $data): Role;
    public function delete(Role $role): bool;
    public function getById(int $id): ?Role;

    public function addRoleClaims(int $roleId, RoleClaim ...$claims): void;
    public function updateRoleClaims(RoleClaim ...$claims): void;
}
