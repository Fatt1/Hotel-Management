<?php

declare(strict_types=1);

namespace App\Actions\Rooms;

use App\Abstractions\Repositories\RoomRepository;
use App\Enums\RoomStatus;
use App\Models\Room;
use Exception;

class CreateRoomAction
{
    public function __construct(
        private RoomRepository $roomRepository
    ) {}

    /**
     * Tạo phòng mới
     * 
     * @param string $name - Số/tên phòng
     * @param int $floorId - ID tầng
     * @param int $roomTypeId - ID loại phòng
     * @return Room
     */
    public function execute(string $name, int $floorId, int $roomTypeId): Room
    {
        // Kiểm tra tên phòng đã tồn tại chưa
        if (Room::where('name', $name)->exists()) {
            throw new Exception("Số phòng đã tồn tại");
        }

        $room = new Room();
        $room->name = $name;
        $room->floor_id = $floorId;
        $room->room_type_id = $roomTypeId;
        $room->status = RoomStatus::READY->value;

        $this->roomRepository->save($room);

        return $room;
    }
}
