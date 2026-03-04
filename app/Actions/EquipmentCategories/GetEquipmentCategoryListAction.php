<?php

declare(strict_types=1);

namespace App\Actions\EquipmentCategories;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class GetEquipmentCategoryListAction
{
    /**
     * Lấy danh sách loại thiết bị (không phân trang)
     */
    public function execute(array $filters = []): Collection
    {
        $query = EquipmentCategory::query();

        // Tìm kiếm theo tên
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Sắp xếp
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->get();
    }

    /**
     * Lấy danh sách loại thiết bị với phân trang
     */
    public function executePaginated(array $filters = [], $perPage = 10)
    {
        $query = EquipmentCategory::query();

        // Tìm kiếm theo tên
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Sắp xếp
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Lấy danh sách loại thiết bị với số lượng thiết bị (không phân trang)
     */
    public function executeWithEquipmentCount(array $filters = []): Collection
    {
        return $this->execute($filters)
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'equipment_count' => Equipment::where('equipment_category_id', $category->id)->count(),
                ];
            });
    }
}
