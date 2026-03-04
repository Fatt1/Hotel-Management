<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Abstractions\Repositories\RoomTypeRepository;
use App\Models\RoomType;

class EloquentRoomTypeRepository implements RoomTypeRepository
{
    /**
     * Tìm loại phòng theo ID
     */
    public function findById(int $id): ?RoomType
    {
        return RoomType::find($id);
    }

    /**
     * Lưu loại phòng (tạo mới hoặc cập nhật)
     */
    public function save(RoomType $roomType): bool
    {
        return $roomType->save();
    }

    /**
     * Xóa loại phòng
     */
    public function delete(RoomType $roomType): bool
    {
        return $roomType->delete();
    }

    /**
     * Lấy tất cả loại phòng
     */
    public function all(): array
    {
        return RoomType::all()->toArray();
    }

    /**
     * Kiểm tra code đã tồn tại
     */
    public function existsByCode(string $code, ?int $excludeId = null): bool
    {
        $query = RoomType::where('code', $code);
        
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
