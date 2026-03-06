<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Abstractions\Repositories\FloorRepository;
use App\Models\Floor;

class EloquentFloorRepository implements FloorRepository
{
    /**
     * Tìm tầng theo ID
     */
    public function findById(int $id): ?Floor
    {
        return Floor::find($id);
    }

    /**
     * Lưu tầng (tạo mới hoặc cập nhật)
     */
    public function save(Floor $floor): bool
    {
        return $floor->save();
    }

    /**
     * Xóa tầng
     */
    public function delete(Floor $floor): bool
    {
        return $floor->delete();
    }
}
