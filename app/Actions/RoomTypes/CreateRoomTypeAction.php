<?php

declare(strict_types=1);

namespace App\Actions\RoomTypes;

use App\Abstractions\Repositories\RoomTypeRepository;
use App\Data\RoomTypeData;
use App\Models\RoomType;
use App\Models\RoomTypeImage;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CreateRoomTypeAction
{
    public function __construct(
        private RoomTypeRepository $roomTypeRepository
    ) {}

    /**
     * Tạo loại phòng mới
     * 
     * @param RoomTypeData $data
     * @param UploadedFile[] $images
     * @param array $amenityIds
     * @param array $equipmentData ['ids' => [], 'quantities' => []]
     * @return RoomType
     */
    public function execute(RoomTypeData $data, array $images = [], array $amenityIds = [], array $equipmentData = []): RoomType
    {
        // Kiểm tra mã code đã tồn tại
        if (RoomType::where('code', $data->code)->exists()) {
            throw new Exception("Mã loại phòng đã tồn tại");
        }

        $roomType = new RoomType();
        $roomType->name = $data->name;
        $roomType->code = $data->code;
        $roomType->is_active = $data->is_active;
        $roomType->adult_quantity = $data->adult_quantity;
        $roomType->child_quantity = $data->child_quantity;
        $roomType->single_bed_quantity = $data->single_bed_quantity;
        $roomType->double_bed_quantity = $data->double_bed_quantity;
        $roomType->description = $data->description ?? null;
        $roomType->width = $data->width;
        $roomType->height = $data->height;
        $roomType->hourly_price = $data->hourly_price;
        $roomType->daily_price = $data->daily_price;

        $this->roomTypeRepository->save($roomType);

        // Xử lý upload và lưu hình ảnh
        $this->saveImages($roomType, $images);

        // Sync amenities
        if (!empty($amenityIds)) {
            $roomType->amenities()->sync($amenityIds);
        }

        // Sync equipment với số lượng
        if (!empty($equipmentData['ids'])) {
            $syncData = [];
            foreach ($equipmentData['ids'] as $equipmentId) {
                $quantity = $equipmentData['quantities'][$equipmentId] ?? 1;
                $syncData[$equipmentId] = ['quantity' => $quantity];
            }
            $roomType->equipments()->sync($syncData);
        }

        return $roomType;
    }

    /**
     * Upload và lưu hình ảnh vào database
     * 
     * @param RoomType $roomType
     * @param UploadedFile[] $images
     */
    private function saveImages(RoomType $roomType, array $images): void
    {
        foreach ($images as $index => $image) {
            if (!$image instanceof UploadedFile) {
                continue;
            }

            // Lấy tên gốc và sanitize
            $originalName = $image->getClientOriginalName();
            $extension = $image->getClientOriginalExtension();
            $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            
            // Sanitize tên file: loại bỏ ký tự đặc biệt, thay space bằng -
            $safeName = preg_replace('/[^a-zA-Z0-9\-_]/', '-', $nameWithoutExt);
            $safeName = preg_replace('/-+/', '-', $safeName); // Loại bỏ multiple dashes
            $safeName = trim($safeName, '-');
            
            // Tạo tên file: timestamp_tenfile.ext
            $filename = time() . '_' . $safeName . '.' . $extension;
            
            // Upload file vào storage/app/public/room-types
            $path = $image->storeAs('room-types', $filename, 'public');

            // Tạo record trong bảng room_type_images
            $roomTypeImage = new RoomTypeImage();
            $roomTypeImage->room_type_id = $roomType->id;
            $roomTypeImage->image_url = $path;
            $roomTypeImage->order = $index;
            $roomTypeImage->save();
        }
    }
}
