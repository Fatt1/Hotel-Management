<?php

declare(strict_types=1);

namespace App\Actions\ServiceGroups;

use App\Abstractions\Repositories\ServiceGroupRepository;
use App\Data\ServiceGroupData;
use App\Models\ServiceGroup;
use Exception;

class UpdateServiceGroupAction
{
    public function __construct(
        private ServiceGroupRepository $serviceGroupRepository
    ) {}

    /**
     * Cập nhật loại dịch vụ
     */
    public function execute(int $id, ServiceGroupData $data): ServiceGroup
    {
        $serviceGroup = $this->serviceGroupRepository->findById($id);

        if (!$serviceGroup) {
            throw new Exception('Loại dịch vụ không tồn tại');
        }

        $serviceGroup->service_name = $data->service_name;

        $this->serviceGroupRepository->save($serviceGroup);
        return $serviceGroup;
    }
}
