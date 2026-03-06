<?php

declare(strict_types=1);

namespace App\Actions\Services;

use App\Abstractions\Repositories\ServiceRepository;
use App\Data\ServiceData;
use App\Models\Service;

class CreateServiceAction
{
    public function __construct(
        private ServiceRepository $serviceRepository
    ) {}

    /**
     * Tạo dịch vụ mới
     */
    public function execute(ServiceData $data): Service
    {
        $service = new Service();
        $service->name = $data->name;
        $service->group_id = $data->group_id;
        $service->unit_price = $data->unit_price;
        $service->unit = $data->unit;

        $this->serviceRepository->save($service);
        return $service;
    }
}
