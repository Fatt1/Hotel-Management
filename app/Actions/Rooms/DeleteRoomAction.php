<?php

declare(strict_types=1);

namespace App\Actions\Rooms;

use App\Models\Room;
use Exception;

class DeleteRoomAction
{
    public function execute(int $id): void
    {
        $room = Room::findOrFail($id);

        if ($room->bookingDetails()->exists()) {
            throw new Exception("Không thể xóa phòng đã có lịch sử đặt phòng");
        }

        $room->delete();
    }
}
