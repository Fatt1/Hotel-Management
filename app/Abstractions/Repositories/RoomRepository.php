<?php

declare(strict_types=1);

namespace App\Abstractions\Repositories;

use App\Models\Room;

interface RoomRepository
{
    /**
     * Tìm phòng theo ID
     */
    public function findById(int $id): ?Room;

    /**
     * Lưu phòng (tạo mới hoặc cập nhật)
     */
    public function save(Room $room): bool;

    /**
     * Xóa phòng
     */
    public function delete(Room $room): bool;
}
