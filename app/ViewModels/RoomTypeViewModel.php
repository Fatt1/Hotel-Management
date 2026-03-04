<?php

declare(strict_types=1);

namespace App\ViewModels;

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
     * Lấy tiện ích (amenities)
     */
    public function amenities(): Collection
    {
        if (!$this->roomType || !$this->roomType->exists) {
            return collect();
        }

        return $this->roomType->amenities()->get();
    }

    /**
     * Lấy thiết bị (equipment) - Mock Data
     * TODO: Khi có Equipment model, bỏ mock data và lấy từ DB
     */
    public function equipment(): Collection
    {
        // Mock data - sẽ thay thế bằng dữ liệu thực từ DB khi triển khai Equipment
        return collect([
            (object)['id' => 1, 'name' => 'Điều hòa Daikin 1.5HP', 'pivot' => (object)['quantity' => 1]],
            (object)['id' => 2, 'name' => 'Tủ lạnh mini Samsung', 'pivot' => (object)['quantity' => 1]],
            (object)['id' => 3, 'name' => 'Smart TV 55 inch', 'pivot' => (object)['quantity' => 1]],
            (object)['id' => 4, 'name' => 'Wifi Router', 'pivot' => (object)['quantity' => 1]],
            (object)['id' => 5, 'name' => 'Máy nước nóng', 'pivot' => (object)['quantity' => 1]],
        ]);
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
