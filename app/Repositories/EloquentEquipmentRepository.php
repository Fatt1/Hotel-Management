<?php

namespace App\Repositories;

use App\Abstractions\Repositories\EquipmentRepository;
use App\Models\Equipment;
use Illuminate\Database\Eloquent\Collection;

class EloquentEquipmentRepository implements EquipmentRepository
{
    public function findById($id): ?Equipment
    {
        return Equipment::find($id);
    }

    public function save(Equipment $equipment): void
    {
        $equipment->save();
    }

    public function delete(Equipment $equipment): void
    {
        $equipment->delete();
    }

    public function all(): Collection
    {
        return Equipment::all();
    }
}
