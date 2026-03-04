<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            ['name' => 'Wifi miễn phí tốc độ cao', 'icon' => 'wifi'],
            ['name' => 'Bộ nội vộ cYč', 'icon' => 'chair'],
            ['name' => 'Điều hòa nhiệt độ', 'icon' => 'ac_unit'],
            ['name' => 'Ban công riêng', 'icon' => 'balcony'],
            ['name' => 'Phòng Gym hiện đại', 'icon' => 'fitness_center'],
            ['name' => 'Dịch vụ Spa và Massage', 'icon' => 'spa'],
            ['name' => 'Hồ bơi ngoài trời', 'icon' => 'pool'],
            ['name' => 'Chỗ đỗ xe miễn phí', 'icon' => 'local_parking'],
            ['name' => 'Nhà hàng 24/24', 'icon' => 'restaurant'],
            ['name' => 'Quầy tiếp tân 24 giờ', 'icon' => 'concierge'],
            ['name' => 'Dịch vụ giặt ủi', 'icon' => 'dry_cleaning'],
            ['name' => 'TV màn hình phẳng', 'icon' => 'tv'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::firstOrCreate(
                ['name' => $amenity['name']],
                ['icon' => $amenity['icon']]
            );
        }
    }
}
