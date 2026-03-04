<?php

namespace Database\Seeders;

use App\Models\EquipmentCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EquipmentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Thiết bị điện tử'],
            ['name' => 'Nội thất phòng'],
            ['name' => 'Tiện ích khách hàng'],
            ['name' => 'Đồ dùng phòng tắm'],
            ['name' => 'Hệ thống thông tin'],
            ['name' => 'Thiết bị phòng bếp'],
            ['name' => 'Hệ thống điều hòa'],
            ['name' => 'Hệ thống an ninh'],
            ['name' => 'Làm vệ sinh'],
            ['name' => 'Trang trí phòng'],
            ['name' => 'Thiết bị tập thể dục'],
            ['name' => 'Nội thất sảnh tiền sảnh'],
        ];

        foreach ($categories as $category) {
            EquipmentCategory::create($category);
        }
    }
}
