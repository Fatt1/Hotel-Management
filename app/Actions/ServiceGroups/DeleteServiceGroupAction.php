<?php

declare(strict_types=1);

namespace App\Actions\ServiceGroups;

use App\Abstractions\Repositories\ServiceGroupRepository;
use App\Models\Service;
use Exception;

class DeleteServiceGroupAction
{
    public function __construct(
        private ServiceGroupRepository $serviceGroupRepository
    ) {}

    /**
     * Xóa loại dịch vụ
     *
     * Business Rule: Không được xóa nếu có dịch vụ thuộc loại này
     */
    public function execute(int $id): void
    {
        $serviceGroup = $this->serviceGroupRepository->findById($id);

        if (!$serviceGroup) {
            throw new Exception('Loại dịch vụ không tồn tại');
        }

        // Kiểm tra có dịch vụ thuộc loại này không
        $serviceCount = Service::where('group_id', $id)->count();

        if ($serviceCount > 0) {
            throw new Exception(
                "Không thể xóa loại dịch vụ này vì đang có {$serviceCount} dịch vụ thuộc loại này"
            );
        }

        $this->serviceGroupRepository->delete($serviceGroup);
    }
}
