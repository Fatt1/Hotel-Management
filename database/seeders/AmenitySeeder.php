<?php

namespace Database\Seeders;

use App\Models\Amenity;
<<<<<<< HEAD
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
=======
>>>>>>> c45d2161fb47281a29ca4729932cf5bb02e574b6
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
<<<<<<< HEAD
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
=======
    public function run(): void
    {
        $amenities = [
            ['name' => 'Bể bơi vô cực', 'icon' => 'fa-swimming-pool'],
            ['name' => 'Spa & Massage', 'icon' => 'fa-spa'],
            ['name' => 'Trợ lý ảo trong phòng', 'icon' => 'fa-robot'],
            ['name' => 'Đưa đón sân bay hạng sang', 'icon' => 'fa-car'],
            ['name' => 'Wifi tốc độ cao', 'icon' => 'fa-wifi'],
            ['name' => 'Bữa sáng Buffet miễn phí', 'icon' => 'fa-utensils'],
            ['name' => 'Phòng Gym 24/7', 'icon' => 'fa-dumbbell'],
            ['name' => 'Dịch vụ giặt ủi', 'icon' => 'fa-tshirt'],
            ['name' => 'Smart TV 65 Inch', 'icon' => 'fa-tv'],
            ['name' => 'Bồn tắm sục Jacuzzi', 'icon' => 'fa-bath'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::create($amenity);
>>>>>>> c45d2161fb47281a29ca4729932cf5bb02e574b6
        }
    }
}
