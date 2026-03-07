<?php

declare(strict_types=1);

namespace App\Actions\EquipmentCategories;

use App\Data\EquipmentCategoryData;
use App\Models\EquipmentCategory;

class CreateEquipmentCategoryAction
{
    public function execute(EquipmentCategoryData $data): EquipmentCategory
    {
        $equipmentCategory = new EquipmentCategory();
        $equipmentCategory->name = $data->name;
        $equipmentCategory->save();
        return $equipmentCategory;
    }
}
