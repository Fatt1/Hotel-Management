<?php

declare(strict_types=1);

namespace App\Actions\Rooms;

use App\Enums\RoomStatus;
use App\Models\Room;

class UpdateRoomStatusAction
{
    public function execute(int $roomId, string $status): Room
    {
        $room = Room::findOrFail($roomId);

        $roomStatus = RoomStatus::tryFrom($status);
        if (!$roomStatus) {
            throw new \InvalidArgumentException('Trạng thái phòng không hợp lệ.');
        }

        $room->status = $roomStatus->value;
        $room->save();

        return $room;
    }
}
