<?php

declare(strict_types=1);

namespace App\Actions\EquipmentCategories;

use App\Abstractions\Repositories\EquipmentCategoryRepository;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use Exception;

class DeleteEquipmentCategoryAction
{
    public function __construct(
        private EquipmentCategoryRepository $equipmentCategoryRepository
    ) {}

    /**
     * Xóa loại thiết bị
     * 
     * Business Rule: Không được xóa nếu có thiết bị thuộc loại này
     */
    public function execute(EquipmentCategory $equipmentCategory): void
    {
        // Kiểm tra có thiết bị thuộc loại này không
        $equipmentCount = Equipment::where('equipment_category_id', $equipmentCategory->id)->count();

        if ($equipmentCount > 0) {
            throw new Exception(
                "Không thể xóa loại thiết bị này vì đang có {$equipmentCount} thiết bị thuộc loại này"
            );
        }

        $this->equipmentCategoryRepository->delete($equipmentCategory);
    }
}
