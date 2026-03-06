<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Abstractions\Repositories\RoomRepository;
use App\Models\Room;

class EloquentRoomRepository implements RoomRepository
{
    /**
     * Tìm phòng theo ID
     */
    public function findById(int $id): ?Room
    {
        return Room::find($id)->with('roomType')->first();
    }

    /**
     * Lưu phòng (tạo mới hoặc cập nhật)
     */
    public function save(Room $room): bool
    {
        return $room->save();
    }

    /**
     * Xóa phòng
     */
    public function delete(Room $room): bool
    {
        return $room->delete();
    }
}
