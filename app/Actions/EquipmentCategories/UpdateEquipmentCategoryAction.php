<?php

declare(strict_types=1);

namespace App\Actions\EquipmentCategories;

use App\Abstractions\Repositories\EquipmentCategoryRepository;
use App\Data\EquipmentCategoryData;
use App\Models\EquipmentCategory;
use Exception;

class UpdateEquipmentCategoryAction
{
    public function __construct(
        private EquipmentCategoryRepository $equipmentCategoryRepository
    ) {}

    /**
     * Cập nhật loại thiết bị
     */
    public function execute(int $id, EquipmentCategoryData $data): EquipmentCategory
    {
        $equipmentCategory = $this->equipmentCategoryRepository->findById($id);

        if (!$equipmentCategory) {
            throw new Exception("Loại thiết bị không tồn tại");
        }

        $equipmentCategory->name = $data->name;

        $this->equipmentCategoryRepository->save($equipmentCategory);
        return $equipmentCategory;
    }
}
