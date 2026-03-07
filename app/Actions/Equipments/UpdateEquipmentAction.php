<?php

declare(strict_types=1);

namespace App\Actions\Equipments;

use App\Data\EquipmentData;
use App\Models\Equipment;

class UpdateEquipmentAction
{
    public function execute(Equipment $equipment, EquipmentData $data): Equipment
    {
        $equipment->name = $data->name;
        $equipment->equipment_category_id = $data->equipment_category_id;
        $equipment->import_price = $data->import_price;
        $equipment->save();
        return $equipment;
    }
}
