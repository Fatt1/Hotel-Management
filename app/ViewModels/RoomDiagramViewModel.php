<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Models\Floor;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RoomDiagramViewModel
{
    private ?int $selectedRoomTypeId;

    public function __construct(Request $request)
    {
        $roomTypeId = $request->get('room_type_id');
        $this->selectedRoomTypeId = $roomTypeId ? (int) $roomTypeId : null;
    }

    /**
     * Lấy danh sách tất cả loại phòng
     */
    public function roomTypes(): Collection
    {
        return RoomType::select('id', 'name', 'code')
            ->withCount('rooms')
            ->orderBy('name')
            ->get();
    }

    /**
     * Lấy ID loại phòng đang được chọn
     */
    public function selectedRoomTypeId(): ?int
    {
        return $this->selectedRoomTypeId;
    }

    /**
     * Lấy loại phòng đang được chọn
     */
    public function selectedRoomType(): ?RoomType
    {
        if (!$this->selectedRoomTypeId) {
            return null;
        }

        return RoomType::find($this->selectedRoomTypeId);
    }

    /**
     * Lấy danh sách tầng với phòng
     * Nếu có chọn loại phòng thì chỉ lấy phòng của loại đó
     */
    public function floors(): Collection
    {
        $query = Floor::query()->orderBy('name');

        if ($this->selectedRoomTypeId) {
            // Lấy tầng có phòng thuộc loại phòng được chọn
            $query->with(['rooms' => function ($q) {
                $q->where('room_type_id', $this->selectedRoomTypeId)
                  ->with('roomType')
                  ->orderBy('name');
            }]);
        } else {
            // Lấy tất cả phòng
            $query->with(['rooms' => function ($q) {
                $q->with('roomType')
                  ->orderBy('name');
            }]);
        }

        return $query->get();
    }

    /**
     * Đếm tổng số phòng
     */
    public function totalRooms(): int
    {
        if ($this->selectedRoomTypeId) {
            return Room::where('room_type_id', $this->selectedRoomTypeId)->count();
        }

        return Room::count();
    }

    /**
     * Đếm tổng số tầng
     */
    public function totalFloors(): int
    {
        return Floor::count();
    }
}
