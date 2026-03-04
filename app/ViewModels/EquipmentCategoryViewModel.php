<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Models\EquipmentCategory;
use Illuminate\Support\Collection;

class EquipmentCategoryViewModel
{
    private ?EquipmentCategory $equipmentCategory;

    public function __construct(EquipmentCategory $equipmentCategory = null)
    {
        $this->equipmentCategory = $equipmentCategory;
    }

    /**
     * Trả về EquipmentCategory (mới hoặc existing)
     */
    public function equipmentCategory(): EquipmentCategory
    {
        return $this->equipmentCategory ?? new EquipmentCategory();
    }

    /**
     * Lấy danh sách tất cả loại thiết bị (dùng cho select/dropdown)
     */
    public function categoryOptions(): Collection
    {
        return EquipmentCategory::select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    /**
     * Kiểm tra loại thiết bị có thiết bị không
     */
    public function hasEquipments(): bool
    {
        if (!$this->equipmentCategory || !$this->equipmentCategory->exists) {
            return false;
        }

        return $this->equipmentCategory->equipments()->exists();
    }

    /**
     * Lấy số lượng thiết bị
     */
    public function equipmentCount(): int
    {
        if (!$this->equipmentCategory || !$this->equipmentCategory->exists) {
            return 0;
        }

        return $this->equipmentCategory->equipments()->count();
    }

    /**
     * Lấy danh sách thiết bị
     */
    public function equipments(): Collection
    {
        if (!$this->equipmentCategory || !$this->equipmentCategory->exists) {
            return collect();
        }

        return $this->equipmentCategory->equipments()->get();
    }
}
