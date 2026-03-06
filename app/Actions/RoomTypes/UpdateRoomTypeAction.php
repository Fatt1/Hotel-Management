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

class UpdateRoomTypeAction
{
    public function __construct(
        private RoomTypeRepository $roomTypeRepository
    ) {}

    /**
     * Cập nhật loại phòng
     * 
     * @param int $id
     * @param RoomTypeData $data
     * @param UploadedFile[] $images
     * @param array $amenityIds
     * @param array $equipmentData ['ids' => [], 'quantities' => []]
     * @param array $deleteImageIds - IDs của ảnh cần xóa
     * @return RoomType
     */
    public function execute(int $id, RoomTypeData $data, array $images = [], array $amenityIds = [], array $equipmentData = [], array $deleteImageIds = []): RoomType
    {
        $roomType = $this->roomTypeRepository->findById($id);
        
        if (!$roomType) {
            throw new Exception("Loại phòng không tồn tại");
        }

        // Kiểm tra mã code đã tồn tại (bỏ qua record hiện tại)
        $existingCode = RoomType::where('code', $data->code)
            ->where('id', '!=', $id)
            ->exists();
        
        if ($existingCode) {
            throw new Exception("Mã loại phòng đã tồn tại");
        }

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

        // Xóa các ảnh được chọn
        if (!empty($deleteImageIds)) {
            $this->deleteSelectedImages($roomType, $deleteImageIds);
        }

        // Nếu có upload ảnh mới, lưu thêm ảnh mới
        if (!empty($images)) {
            $this->saveImages($roomType, $images);
        }

        // Sync amenities (luôn sync để cập nhật thay đổi)
        $roomType->amenities()->sync($amenityIds);

        // Sync equipment với số lượng
        $syncData = [];
        if (!empty($equipmentData['ids'])) {
            foreach ($equipmentData['ids'] as $equipmentId) {
                $quantity = $equipmentData['quantities'][$equipmentId] ?? 1;
                $syncData[$equipmentId] = ['quantity' => $quantity];
            }
        }
        $roomType->equipments()->sync($syncData);

        return $roomType;
    }

    /**
     * Xóa các ảnh được chọn
     */
    private function deleteSelectedImages(RoomType $roomType, array $imageIds): void
    {
        $imagesToDelete = $roomType->images()->whereIn('id', $imageIds)->get();
        
        foreach ($imagesToDelete as $image) {
            // Xóa file trong storage
            Storage::disk('public')->delete($image->image_url);
            // Xóa record trong database
            $image->delete();
        }
    }

    /**
     * Upload và lưu hình ảnh vào database
     * 
     * @param RoomType $roomType
     * @param UploadedFile[] $images
     */
    private function saveImages(RoomType $roomType, array $images): void
    {
        // Lấy order cao nhất hiện tại
        $maxOrder = $roomType->images()->max('order') ?? -1;
        
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
            $roomTypeImage->order = $maxOrder + $index + 1;
            $roomTypeImage->save();
        }
    }
}
