<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        $roomTypes = [
            [
                'name'                => 'Phòng Tiêu Chuẩn (Standard)',
                'code'                => 'STD',
                'adult_quantity'      => 2,
                'child_quantity'      => 1,
                'single_bed_quantity' => 2,
                'double_bed_quantity' => 0,
                'width'               => 4.0,
                'height'              => 5.0,
                'hourly_price'        => 50000.00,
                'daily_price'         => 300000.00,
                'description'         => 'Phòng tiêu chuẩn với tiện nghi cơ bản, phù hợp cho cặp đôi hoặc khách đi công tác nhỏ.'
            ],
            [
                'name'                => 'Phòng Cao Cấp (Deluxe)',
                'code'                => 'DLX',
                'adult_quantity'      => 2,
                'child_quantity'      => 2,
                'single_bed_quantity' => 0,
                'double_bed_quantity' => 1,
                'width'               => 5.0,
                'height'              => 6.0,
                'hourly_price'        => 80000.00,
                'daily_price'         => 500000.00,
                'description'         => 'Phòng cao cấp rộng rãi, thiết kế hiện đại và ban công hướng nhìn thành phố.'
            ],
            [
                'name'                => 'Phòng Gia Đình (Family)',
                'code'                => 'FAM',
                'adult_quantity'      => 4,
                'child_quantity'      => 2,
                'single_bed_quantity' => 2,
                'double_bed_quantity' => 1,
                'width'               => 8.0,
                'height'              => 8.0,
                'hourly_price'        => 120000.00,
                'daily_price'         => 700000.00,
                'description'         => 'Phòng dành riêng cho gia đình với không gian mở và khu vực sinh hoạt chung ấm cúng.'
            ],
            [
                'name'                => 'Phòng Tổng Thống (Presidential)',
                'code'                => 'PRE',
                'adult_quantity'      => 2,
                'child_quantity'      => 2,
                'single_bed_quantity' => 0,
                'double_bed_quantity' => 1,
                'width'               => 12.0,
                'height'              => 15.0,
                'hourly_price'        => 300000.00,
                'daily_price'         => 2500000.00,
                'description'         => 'Trải nghiệm đỉnh cao với nội thất xa hoa, bồn tắm sục Jacuzzi và view biển toàn cảnh.'
            ],
        ];

        foreach ($roomTypes as $roomType) {
            RoomType::create($roomType);
        }
    }
}
