<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
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
        }
    }
}
