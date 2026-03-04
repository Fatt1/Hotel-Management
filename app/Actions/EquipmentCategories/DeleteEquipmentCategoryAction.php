<?php

declare(strict_types=1);

namespace App\Actions\EquipmentCategories;

use App\Abstractions\Repositories\EquipmentCategoryRepository;
use App\Models\Equipment;
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
    public function execute(int $id): void
    {
        $equipmentCategory = $this->equipmentCategoryRepository->findById($id);

        if (!$equipmentCategory) {
            throw new Exception("Loại thiết bị không tồn tại");
        }

        // Kiểm tra có thiết bị thuộc loại này không
        $equipmentCount = Equipment::where('equipment_category_id', $id)->count();

        if ($equipmentCount > 0) {
            throw new Exception(
                "Không thể xóa loại thiết bị này vì đang có {$equipmentCount} thiết bị thuộc loại này"
            );
        }

        $this->equipmentCategoryRepository->delete($equipmentCategory);
    }
}
