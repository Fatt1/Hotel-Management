<?php

namespace Database\Seeders;

use App\Models\Dining;
use Illuminate\Database\Seeder;

class DiningSeeder extends Seeder
{
    public function run(): void
    {
        $dinings = [
            [
                'name' => 'Nhà Hàng Cung Đình',
                'description' => 'Trải nghiệm tinh hoa ẩm thực Việt trong không gian truyền thống sang trọng.',
                'image' => 'images/dining/cung-dinh.jpg',
                'opening_hours' => '10:00 - 22:00',
                'location' => 'Tầng 1',
                'is_active' => true,
            ],
            [
                'name' => 'Nhà Hàng Ocean Breeze',
                'description' => 'Chuyên các loại hải sản tươi sống và tiệc buffet cao cấp phong cách Âu Á.',
                'image' => 'images/dining/ocean-breeze.jpg',
                'opening_hours' => '06:00 - 23:00',
                'location' => 'Tầng 2',
                'is_active' => true,
            ],
            [
                'name' => 'Skyline Bar',
                'description' => 'Tận hưởng cocktail tuyệt hảo và ngắm nhìn toàn cảnh thành phố về đêm.',
                'image' => 'images/dining/skyline-bar.jpg',
                'opening_hours' => '17:00 - 02:00',
                'location' => 'Tầng Thượng (Rooftop)',
                'is_active' => true,
            ]
        ];

        foreach ($dinings as $dining) {
            Dining::create($dining);
        }
    }
}
