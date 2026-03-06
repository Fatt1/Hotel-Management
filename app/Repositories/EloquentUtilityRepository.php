<?php

namespace App\Repositories;

use App\Abstractions\Repositories\UtilityRepository;
use App\Models\Utility;

class EloquentUtilityRepository implements UtilityRepository
{
    public function findById(int $id): ?Utility
    {
        return Utility::find($id);
    }

    public function save(Utility $utility): bool
    {
        // Eloquent tự phân biệt INSERT / UPDATE dựa vào $utility->exists
        return $utility->save();
    }

    public function delete(Utility $utility): bool
    {
        return $utility->delete();
    }
}
