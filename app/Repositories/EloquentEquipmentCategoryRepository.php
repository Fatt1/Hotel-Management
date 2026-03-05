<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Abstractions\Repositories\EquipmentCategoryRepository;
use App\Models\EquipmentCategory;

class EloquentEquipmentCategoryRepository implements EquipmentCategoryRepository
{
    public function findById(int $id): ?EquipmentCategory
    {
        return EquipmentCategory::find($id);
    }

    public function save(EquipmentCategory $equipmentCategory): bool
    {
        return $equipmentCategory->save();
    }

    public function delete(EquipmentCategory $equipmentCategory): bool
    {
        return $equipmentCategory->delete();
    }

    public function all(): array
    {
        return EquipmentCategory::all()->toArray();
    }

    public function existsByCode(string $code, ?int $excludeId = null): bool
    {
        $query = EquipmentCategory::where('code', $code);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
