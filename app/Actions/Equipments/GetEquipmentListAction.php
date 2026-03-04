<?php

declare(strict_types=1);

namespace App\Actions\Equipments;

use App\Models\Equipment;

class GetEquipmentListAction
{
    /**
     * Lấy danh sách thiết bị (không phân trang)
     */
    public function execute(array $filters = []): object
    {
        $query = Equipment::with('category')->orderBy('name', 'asc');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['equipment_category_id'])) {
            $query->where('equipment_category_id', $filters['equipment_category_id']);
        }

        return $query->get();
    }

    /**
     * Lấy danh sách thiết bị với phân trang
     */
    public function executePaginated(array $filters = [], $perPage = 10)
    {
        $query = Equipment::with('category')->orderBy('name', 'asc');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['equipment_category_id'])) {
            $query->where('equipment_category_id', $filters['equipment_category_id']);
        }

        return $query->paginate($perPage);
    }
}
