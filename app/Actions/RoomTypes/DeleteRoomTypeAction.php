<?php

declare(strict_types=1);

namespace App\Actions\RoomTypes;

use App\Models\Room;
use App\Models\RoomType;
use Exception;

class DeleteRoomTypeAction
{
    public function execute(int $id): void
    {
        $roomType = RoomType::findOrFail($id);

        $roomCount = Room::where('room_type_id', $id)->count();

        if ($roomCount > 0) {
            throw new Exception(
                "Không thể xóa loại phòng này vì đang có {$roomCount} phòng thuộc loại này"
            );
        }

        $roomType->delete();
    }
}
