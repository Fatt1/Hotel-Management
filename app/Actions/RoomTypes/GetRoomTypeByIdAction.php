<?php

namespace App\Actions\RoomTypes;

use App\Models\RoomType;

class GetRoomTypeByIdAction
{
    public function handle(int $id)
    {
        // Lấy loại phòng theo ID
        return RoomType::find($id);
    }
}