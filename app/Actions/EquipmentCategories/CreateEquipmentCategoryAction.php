<?php

declare(strict_types=1);

namespace App\Actions\EquipmentCategories;

use App\Abstractions\Repositories\EquipmentCategoryRepository;
use App\Data\EquipmentCategoryData;
use App\Models\EquipmentCategory;

class CreateEquipmentCategoryAction
{
    public function __construct(
        private EquipmentCategoryRepository $equipmentCategoryRepository
    ) {}

    /**
     * Tạo loại thiết bị mới
     */
    public function execute(EquipmentCategoryData $data): EquipmentCategory
    {
        $equipmentCategory = new EquipmentCategory();
        $equipmentCategory->name = $data->name;

        $this->equipmentCategoryRepository->save($equipmentCategory);
        return $equipmentCategory;
    }
}
