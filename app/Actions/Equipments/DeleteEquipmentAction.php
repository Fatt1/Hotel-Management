<?php

declare(strict_types=1);

namespace App\Actions\Equipments;

use App\Abstractions\Repositories\EquipmentRepository;
use App\Models\Equipment;
use Exception;

class DeleteEquipmentAction
{
    public function __construct(
        private EquipmentRepository $equipmentRepository
    ) {}

    /**
     * Xóa thiết bị
     */
    public function execute(Equipment $equipment): void
    {
        // Kiểm tra xem thiết bị có được sử dụng trong phòng không
        $relatedCount = $equipment->roomTypes()->count();
        if ($relatedCount > 0) {
            throw new Exception("Không thể xóa thiết bị này vì đang được sử dụng trong $relatedCount loại phòng");
        }

        $this->equipmentRepository->delete($equipment);
    }
}
