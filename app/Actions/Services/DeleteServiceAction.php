<?php

declare(strict_types=1);

namespace App\Actions\Services;

use App\Abstractions\Repositories\ServiceRepository;
use Exception;

class DeleteServiceAction
{
    public function __construct(
        private ServiceRepository $serviceRepository
    ) {}

    /**
     * Xóa dịch vụ
     */
    public function execute(int $id): void
    {
        $service = $this->serviceRepository->findById($id);

        if (!$service) {
            throw new Exception('Dịch vụ không tồn tại');
        }

        $this->serviceRepository->delete($service);
    }
}
