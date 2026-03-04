<?php

declare(strict_types=1);

namespace App\Abstractions\Repositories;

use App\Models\EquipmentCategory;

interface EquipmentCategoryRepository
{
    /**
     * Lấy loại thiết bị theo ID
     */
    public function findById(int $id): ?EquipmentCategory;

    /**
     * Lưu loại thiết bị (insert / update)
     */
    public function save(EquipmentCategory $equipmentCategory): bool;

    /**
     * Xóa loại thiết bị
     */
    public function delete(EquipmentCategory $equipmentCategory): bool;

    /**
     * Lấy tất cả loại thiết bị
     */
    public function all(): array;

    /**
     * Kiểm tra mã loại thiết bị đã tồn tại chưa
     */
    public function existsByCode(string $code, ?int $excludeId = null): bool;
}
