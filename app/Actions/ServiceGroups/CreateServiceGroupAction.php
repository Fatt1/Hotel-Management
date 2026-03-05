<?php

declare(strict_types=1);

namespace App\Actions\ServiceGroups;

use App\Abstractions\Repositories\ServiceGroupRepository;
use App\Data\ServiceGroupData;
use App\Models\ServiceGroup;

class CreateServiceGroupAction
{
    public function __construct(
        private ServiceGroupRepository $serviceGroupRepository
    ) {}

    /**
     * Tạo loại dịch vụ mới
     */
    public function execute(ServiceGroupData $data): ServiceGroup
    {
        $serviceGroup = new ServiceGroup();
        $serviceGroup->service_name = $data->service_name;

        $this->serviceGroupRepository->save($serviceGroup);
        return $serviceGroup;
    }
}
