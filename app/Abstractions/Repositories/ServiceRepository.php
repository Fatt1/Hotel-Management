<?php

declare(strict_types=1);

namespace App\Abstractions\Repositories;

use App\Models\Service;

interface ServiceRepository
{
    /**
     * Lấy dịch vụ theo ID
     */
    public function findById(int $id): ?Service;

    /**
     * Lưu dịch vụ (insert / update)
     */
    public function save(Service $service): bool;

    /**
     * Xóa dịch vụ
     */
    public function delete(Service $service): bool;
}
