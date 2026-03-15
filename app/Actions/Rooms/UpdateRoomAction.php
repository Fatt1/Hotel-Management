<?php

declare(strict_types=1);

namespace App\Actions\Rooms;

use App\Models\Room;
use Exception;

class UpdateRoomAction
{
    public function execute(int $id, string $name, int $roomTypeId): Room
    {
        $room = Room::findOrFail($id);

        if (Room::where('name', $name)->where('id', '!=', $id)->exists()) {
            throw new Exception("Số phòng đã tồn tại");
        }

        $room->name = $name;
        $room->room_type_id = $roomTypeId;
        $room->save();

        return $room;
    }
}
