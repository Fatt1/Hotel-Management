<?php

namespace App\Repositories;

use App\Abstractions\Repositories\UtilityRepository;
use App\Models\Utility;

class EloquentUtilityRepository implements UtilityRepository
{
    public function findById(int $id)
    {
        return Utility::find($id);
    }

    public function save(array $data)
    {
        return Utility::create($data);
    }

    public function delete(int $id)
    {
        return Utility::destroy($id);
    }

    public function all()
    {
        return Utility::all();
    }
}
