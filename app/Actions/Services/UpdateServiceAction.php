<?php

declare(strict_types=1);

namespace App\Actions\Services;

use App\Abstractions\Repositories\ServiceRepository;
use App\Data\ServiceData;
use App\Models\Service;
use Exception;

class UpdateServiceAction
{
    public function __construct(
        private ServiceRepository $serviceRepository
    ) {}

    /**
     * Cập nhật dịch vụ
     */
    public function execute(int $id, ServiceData $data): Service
    {
        $service = $this->serviceRepository->findById($id);

        if (!$service) {
            throw new Exception('Dịch vụ không tồn tại');
        }

        $service->name = $data->name;
        $service->group_id = $data->group_id;
        $service->unit_price = $data->unit_price;
        $service->unit = $data->unit;

        $this->serviceRepository->save($service);
        return $service;
    }
}
