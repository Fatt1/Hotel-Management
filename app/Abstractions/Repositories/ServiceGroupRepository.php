<?php

declare(strict_types=1);

namespace App\Abstractions\Repositories;

use App\Models\ServiceGroup;

interface ServiceGroupRepository
{
    /**
     * Lấy loại dịch vụ theo ID
     */
    public function findById(int $id): ?ServiceGroup;

    /**
     * Lưu loại dịch vụ (insert / update)
     */
    public function save(ServiceGroup $serviceGroup): bool;

    /**
     * Xóa loại dịch vụ
     */
    public function delete(ServiceGroup $serviceGroup): bool;
}
