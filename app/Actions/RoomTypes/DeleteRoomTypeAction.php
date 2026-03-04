<?php

declare(strict_types=1);

namespace App\Actions\RoomTypes;

use App\Abstractions\Repositories\RoomTypeRepository;
use App\Models\Room;
use Exception;

class DeleteRoomTypeAction
{
    public function __construct(
        private RoomTypeRepository $roomTypeRepository
    ) {}

    /**
     * Xóa loại phòng
     * 
     * Business Rule: Không được xóa nếu có phòng thuộc loại này
     */
    public function execute(int $id): void
    {
        $roomType = $this->roomTypeRepository->findById($id);
        
        if (!$roomType) {
            throw new Exception("Loại phòng không tồn tại");
        }

        // Kiểm tra có phòng thuộc loại này không
        $roomCount = Room::where('room_type_id', $id)->count();

        if ($roomCount > 0) {
            throw new Exception(
                "Không thể xóa loại phòng này vì đang có {$roomCount} phòng thuộc loại này"
            );
        }

        $this->roomTypeRepository->delete($roomType);
    }
}
