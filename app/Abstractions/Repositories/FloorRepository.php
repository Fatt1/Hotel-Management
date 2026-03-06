<?php

declare(strict_types=1);

namespace App\Abstractions\Repositories;

use App\Models\Floor;

interface FloorRepository
{
    /**
     * Tìm tầng theo ID
     */
    public function findById(int $id): ?Floor;

    /**
     * Lưu tầng (tạo mới hoặc cập nhật)
     */
    public function save(Floor $floor): bool;

    /**
     * Xóa tầng
     */
    public function delete(Floor $floor): bool;
}
