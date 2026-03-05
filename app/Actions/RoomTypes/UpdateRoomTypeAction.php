<?php

declare(strict_types=1);

namespace App\Actions\RoomTypes;

use App\Abstractions\Repositories\RoomTypeRepository;
use App\Data\RoomTypeData;
use App\Models\RoomType;
use Exception;

class UpdateRoomTypeAction
{
    public function __construct(
        private RoomTypeRepository $roomTypeRepository
    ) {}

    /**
     * Cập nhật loại phòng
     */
    public function execute(int $id, RoomTypeData $data): RoomType
    {
        $roomType = $this->roomTypeRepository->findById($id);
        
        if (!$roomType) {
            throw new Exception("Loại phòng không tồn tại");
        }

        $roomType->name = $data->name;
        $roomType->code = $data->code;
        $roomType->adult_quantity = $data->adult_quantity;
        $roomType->child_quantity = $data->child_quantity;
        $roomType->single_bed_quantity = $data->single_bed_quantity;
        $roomType->double_bed_quantity = $data->double_bed_quantity;
        $roomType->description = $data->description ?? null;
        $roomType->width = $data->width;
        $roomType->height = $data->height;
        $roomType->hourly_price = $data->hourly_price;
        $roomType->daily_price = $data->daily_price;

        $this->roomTypeRepository->save($roomType);

        return $roomType;
    }
}
