<?php

namespace App\ViewModels;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use Illuminate\Support\Collection;

class EquipmentViewModel
{
    private ?Equipment $equipment;

    public function __construct(Equipment $equipment = null)
    {
        $this->equipment = $equipment;
    }

    /**
     * Trả về equipment (mới hoặc existing)
     */
    public function equipment(): Equipment
    {
        return $this->equipment ?? new Equipment();
    }

    /**
     * Danh sách loại thiết bị để dropdown
     */
    public function categories(): Collection
    {
        return EquipmentCategory::select('id', 'name')
            ->orderBy('name')
            ->get();
    }
}
