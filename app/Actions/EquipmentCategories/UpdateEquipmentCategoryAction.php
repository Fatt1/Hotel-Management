<?php

declare(strict_types=1);

namespace App\Actions\EquipmentCategories;

use App\Data\EquipmentCategoryData;
use App\Models\EquipmentCategory;
use Exception;

class UpdateEquipmentCategoryAction
{
    public function execute(int $id, EquipmentCategoryData $data): EquipmentCategory
    {
        $equipmentCategory = EquipmentCategory::findOrFail($id);
        $equipmentCategory->name = $data->name;
        $equipmentCategory->save();
        return $equipmentCategory;
    }
}
