<?php

declare(strict_types=1);

namespace App\Actions\Rooms;

use App\Abstractions\Repositories\RoomRepository;
use App\Models\Room;
use Exception;

class UpdateRoomAction
{
    public function __construct(
        private RoomRepository $roomRepository
    ) {}

    /**
     * Cập nhật phòng
     * 
     * @param int $id
     * @param string $name
     * @param int $roomTypeId
     * @return Room
     */
    public function execute(int $id, string $name, int $roomTypeId): Room
    {
        $room = $this->roomRepository->findById($id);

        if (!$room) {
            throw new Exception("Phòng không tồn tại");
        }

        // Kiểm tra tên phòng đã tồn tại chưa (bỏ qua phòng hiện tại)
        if (Room::where('name', $name)->where('id', '!=', $id)->exists()) {
            throw new Exception("Số phòng đã tồn tại");
        }

        $room->name = $name;
        $room->room_type_id = $roomTypeId;

        $this->roomRepository->save($room);

        return $room;
    }
}
