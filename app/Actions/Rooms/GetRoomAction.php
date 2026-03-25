<?php

namespace App\Actions\Rooms;

use App\Models\Room;

class GetRoomAction
{
    public function handle(int $roomId)
    {
        return Room::with(['roomType', 'floor'])->findOrFail($roomId);
    }
}
