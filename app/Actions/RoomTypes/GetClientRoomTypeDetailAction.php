<?php

declare(strict_types=1);

namespace App\Actions\RoomTypes;

use App\Models\RoomType;

class GetClientRoomTypeDetailAction
{
    /**
     * Lấy chi tiết một loại phòng dành cho client.
     */
    public function execute(int $id): RoomType
    {
        return RoomType::with(['images', 'amenities', 'rooms'])
            ->findOrFail($id);
    }
}
