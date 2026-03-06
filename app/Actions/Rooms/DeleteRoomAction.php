<?php

declare(strict_types=1);

namespace App\Actions\Rooms;

use App\Abstractions\Repositories\RoomRepository;
use Exception;

class DeleteRoomAction
{
    public function __construct(
        private RoomRepository $roomRepository
    ) {}

    /**
     * Xóa phòng
     * 
     * @param int $id
     * @return void
     */
    public function execute(int $id): void
    {
        $room = $this->roomRepository->findById($id);

        if (!$room) {
            throw new Exception("Phòng không tồn tại");
        }

        // Kiểm tra phòng có booking không
        if ($room->bookingDetails()->exists()) {
            throw new Exception("Không thể xóa phòng đã có lịch sử đặt phòng");
        }

        $this->roomRepository->delete($room);
    }
}
