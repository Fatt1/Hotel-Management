<?php

declare(strict_types=1);

namespace App\Abstractions\Repositories;

use App\Models\RoomType;

interface RoomTypeRepository
{
    /**
     * Tìm loại phòng theo ID
     */
    public function findById(int $id): ?RoomType;

    /**
     * Lưu loại phòng (tạo mới hoặc cập nhật)
     */
    public function save(RoomType $roomType): bool;

    /**
     * Xóa loại phòng
     */
    public function delete(RoomType $roomType): bool;

    /**
     * Lấy tất cả loại phòng
     */
    public function all(): array;

    /**
     * Kiểm tra code đã tồn tại
     */
    public function existsByCode(string $code, ?int $excludeId = null): bool;
}
