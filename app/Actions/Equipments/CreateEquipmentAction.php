<?php

declare(strict_types=1);

namespace App\Actions\Equipments;

use App\Abstractions\Repositories\EquipmentRepository;
use App\Data\EquipmentData;
use App\Models\Equipment;

class CreateEquipmentAction
{
    public function __construct(
        private EquipmentRepository $equipmentRepository
    ) {}

    /**
     * Tạo thiết bị mới
     */
    public function execute(EquipmentData $data): Equipment
    {
        $equipment = new Equipment();
        $equipment->name = $data->name;
        $equipment->equipment_category_id = $data->equipment_category_id;
        $equipment->import_price = $data->import_price;

        $this->equipmentRepository->save($equipment);
        return $equipment;
    }
}
