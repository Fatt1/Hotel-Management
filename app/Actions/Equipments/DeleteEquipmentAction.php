<?php

declare(strict_types=1);

namespace App\Actions\Equipments;

use App\Models\Equipment;
use Exception;

class DeleteEquipmentAction
{
    public function execute(Equipment $equipment): void
    {
        $relatedCount = $equipment->roomTypes()->count();
        if ($relatedCount > 0) {
            throw new Exception("Không thể xóa thiết bị này vì đang được sử dụng trong $relatedCount loại phòng");
        }

        $equipment->delete();
    }
}
