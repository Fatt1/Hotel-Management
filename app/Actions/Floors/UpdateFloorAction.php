<?php

declare(strict_types=1);

namespace App\Actions\Floors;

use App\Abstractions\Repositories\FloorRepository;
use App\Models\Floor;
use Exception;

class UpdateFloorAction
{
    public function __construct(
        private FloorRepository $floorRepository
    ) {}

    /**
     * Cập nhật tầng
     * 
     * @param int $id
     * @param string $name
     * @return Floor
     */
    public function execute(int $id, string $name): Floor
    {
        $floor = $this->floorRepository->findById($id);

        if (!$floor) {
            throw new Exception("Tầng không tồn tại");
        }

        // Kiểm tra tên tầng đã tồn tại chưa (bỏ qua tầng hiện tại)
        if (Floor::where('name', $name)->where('id', '!=', $id)->exists()) {
            throw new Exception("Tên tầng đã tồn tại");
        }

        $floor->name = $name;

        $this->floorRepository->save($floor);

        return $floor;
    }
}
