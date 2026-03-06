<?php

declare(strict_types=1);

namespace App\Actions\Floors;

use App\Abstractions\Repositories\FloorRepository;
use Exception;

class DeleteFloorAction
{
    public function __construct(
        private FloorRepository $floorRepository
    ) {}

    /**
     * Xóa tầng
     * 
     * @param int $id
     * @return void
     */
    public function execute(int $id): void
    {
        $floor = $this->floorRepository->findById($id);

        if (!$floor) {
            throw new Exception("Tầng không tồn tại");
        }

        // Kiểm tra tầng có phòng không
        if ($floor->rooms()->count() > 0) {
            throw new Exception("Không thể xóa tầng có phòng. Vui lòng xóa hết phòng trước.");
        }

        $this->floorRepository->delete($floor);
    }
}
