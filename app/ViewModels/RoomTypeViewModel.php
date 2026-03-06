<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Models\Amenity;
use App\Models\Equipment;
use App\Models\RoomType;
use Illuminate\Support\Collection;

class RoomTypeViewModel
{
    private ?RoomType $roomType;

    public function __construct(RoomType $roomType = null)
    {
        $this->roomType = $roomType;
    }

    /**
     * Trả về RoomType (mới hoặc existing)
     */
    public function roomType(): RoomType
    {
        return $this->roomType ?? new RoomType();
    }

    /**
     * Lấy danh sách tất cả loại phòng (dùng cho select/dropdown)
     */
    public function roomTypeOptions(): Collection
    {
        return RoomType::select('id', 'name', 'code')
            ->orderBy('name')
            ->get();
    }

    /**
     * Kiểm tra loại phòng có phòng không
     */
    public function hasRooms(): bool
    {
        if (!$this->roomType || !$this->roomType->exists) {
            return false;
        }

        return $this->roomType->rooms()->exists();
    }

    /**
     * Lấy số lượng phòng
     */
    public function totalRooms(): int
    {
        if (!$this->roomType || !$this->roomType->exists) {
            return 0;
        }

        return $this->roomType->rooms()->count();
    }

    /**
     * Lấy số lượng phòng trống
     */
    public function availableRooms(): int
    {
        if (!$this->roomType || !$this->roomType->exists) {
            return 0;
        }

        return $this->roomType->rooms()
            ->where('status', 'available')
            ->count();
    }

    /**
     * Lấy thông tin kích thước
     */
    public function dimensions(): array
    {
        return [
            'width' => $this->roomType?->width,
            'height' => $this->roomType?->height,
            'area' => $this->roomType ? ($this->roomType->width * $this->roomType->height) : 0,
        ];
    }

    /**
     * Lấy thông tin giá
     */
    public function pricing(): array
    {
        return [
            'hourly_price' => $this->roomType?->hourly_price,
            'daily_price' => $this->roomType?->daily_price,
        ];
    }

    /**
     * Lấy thông tin sức chứa
     */
    public function capacity(): array
    {
        return [
            'adult_quantity' => $this->roomType?->adult_quantity,
            'child_quantity' => $this->roomType?->child_quantity,
            'single_bed_quantity' => $this->roomType?->single_bed_quantity,
            'double_bed_quantity' => $this->roomType?->double_bed_quantity,
            'total_guests' => ($this->roomType?->adult_quantity ?? 0) + ($this->roomType?->child_quantity ?? 0),
        ];
    }

    /**
     * Lấy tiện ích (amenities) của loại phòng này
     */
    public function amenities(): Collection
    {
        if (!$this->roomType || !$this->roomType->exists) {
            return collect();
        }

        return $this->roomType->amenities()->get();
    }

    /**
     * Lấy TẤT CẢ tiện ích từ database (cho modal picker)
     */
    public function allAmenities(): Collection
    {
        return Amenity::orderBy('name')->get();
    }

    /**
     * Lấy thiết bị (equipment) của loại phòng này
     */
    public function equipment(): Collection
    {
        if (!$this->roomType || !$this->roomType->exists) {
            return collect();
        }

        return $this->roomType->equipments()->with('category')->get();
    }

    /**
     * Lấy TẤT CẢ thiết bị từ database (cho modal picker)
     */
    public function allEquipments(): Collection
    {
        return Equipment::with('category')->orderBy('name')->get();
    }

    /**
     * Lấy hình ảnh
     */
    public function images(): Collection
    {
        if (!$this->roomType || !$this->roomType->exists) {
            return collect();
        }

        return $this->roomType->images()->get();
    }

    /**
     * Lấy tất cả hình ảnh như URL
     */
    public function imageUrls(): array
    {
        return $this->images()
            ->map(fn($image) => $image->image_path ?? $image->url)
            ->toArray();
    }
}
