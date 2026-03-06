<?php

namespace App\Abstractions\Repositories;

use App\Models\Utility;

interface UtilityRepository
{
    public function findById(int $id): ?Utility;

    public function save(Utility $utility): bool;

    public function delete(Utility $utility): bool;
}
