<?php

declare(strict_types=1);

namespace App\Actions\EquipmentCategories;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use Exception;

class DeleteEquipmentCategoryAction
{
    public function execute(EquipmentCategory $equipmentCategory): void
    {
        $equipmentCount = Equipment::where('equipment_category_id', $equipmentCategory->id)->count();

        if ($equipmentCount > 0) {
            throw new Exception(
                "Không thể xóa loại thiết bị này vì đang có {$equipmentCount} thiết bị thuộc loại này"
            );
        }

        $equipmentCategory->delete();
    }
}
