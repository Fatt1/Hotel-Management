<?php

declare(strict_types=1);

namespace App\Actions\RoomTypes;

use App\Models\RoomType;
use Illuminate\Support\Collection;

class GetRoomTypeListAction
{
    /**
     * Lấy danh sách loại phòng
     */
    public function execute(array $filters = []): Collection
    {
        $query = RoomType::query();

        // Filter theo tên (tìm kiếm)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter theo trạng thái
        if (isset($filters['status']) && $filters['status'] !== '') {
            $status = (int) $filters['status'];
            if (in_array($status, [0, 1], true)) {
                $query->where('is_active', $status);
            }
        }

        // Filter theo giá (từ -> đến)
        if (!empty($filters['min_price'])) {
            $query->where('hourly_price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('hourly_price', '<=', $filters['max_price']);
        }

        // Sắp xếp
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->get();
    }

    /**
     * Lấy loại phòng với số lượng phòng
     */
    public function executeWithRoomCount(array $filters = []): Collection
    {
        return $this->execute($filters)->map(function ($roomType) {
            return [
                'id' => $roomType->id,
                'name' => $roomType->name,
                'code' => $roomType->code,
                'adult_quantity' => $roomType->adult_quantity,
                'child_quantity' => $roomType->child_quantity,
                'single_bed_quantity' => $roomType->single_bed_quantity,
                'double_bed_quantity' => $roomType->double_bed_quantity,
                'description' => $roomType->description,
                'width' => $roomType->width,
                'height' => $roomType->height,
                'hourly_price' => $roomType->hourly_price,
                'daily_price' => $roomType->daily_price,
                'total_rooms' => $roomType->rooms()->count(),
                'available_rooms' => $roomType->rooms()->where('status', 'available')->count(),
                'status' => (int) $roomType->is_active,
            ];
        });
    }
}
