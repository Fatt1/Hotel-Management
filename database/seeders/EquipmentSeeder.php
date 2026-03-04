<?php

namespace Database\Seeders;

use App\Models\Equipment;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipments = [
            [
                'name' => 'Smart TV Samsung 4K 55 inch',
                'equipment_category_id' => 1,
                'import_price' => 12500000,
            ],
            [
                'name' => 'Ghế Sofa đơn phong cách Urban',
                'equipment_category_id' => 2,
                'import_price' => 4200000,
            ],
            [
                'name' => 'Máy điều hòa Panasonic Inverter',
                'equipment_category_id' => 1,
                'import_price' => 9800000,
            ],
            [
                'name' => 'Máy pha cà phê Nespresso Mini',
                'equipment_category_id' => 6,
                'import_price' => 3500000,
            ],
            [
                'name' => 'Tủ lạnh mini Midea 150L',
                'equipment_category_id' => 6,
                'import_price' => 5200000,
            ],
            [
                'name' => 'Bộ vệ sinh Villeroy & Boch hiện đại',
                'equipment_category_id' => 5,
                'import_price' => 8900000,
            ],
            [
                'name' => 'Hệ thống âm thanh Bose SoundLink',
                'equipment_category_id' => 1,
                'import_price' => 6700000,
            ],
            [
                'name' => 'Giường đôi King Size cao cấp',
                'equipment_category_id' => 2,
                'import_price' => 25000000,
            ],
            [
                'name' => 'Bàn trang điểm gương LED',
                'equipment_category_id' => 2,
                'import_price' => 3800000,
            ],
            [
                'name' => 'Máy giặt LG 9kg Dragon Inverter',
                'equipment_category_id' => 1,
                'import_price' => 12300000,
            ],
            [
                'name' => 'Bộ rèm cửa tự động Dooya',
                'equipment_category_id' => 2,
                'import_price' => 7500000,
            ],
            [
                'name' => 'Camera giám sát Hikvision 4MP',
                'equipment_category_id' => 8,
                'import_price' => 2500000,
            ],
        ];

        foreach ($equipments as $equipment) {
            Equipment::create($equipment);
        }
    }
}
