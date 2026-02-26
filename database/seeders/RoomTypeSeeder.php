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
                'name'                => 'Standard',
                'code'                => 'STD',
                'adult_quantity'      => 2,
                'child_quantity'      => 1,
                'single_bed_quantity' => 2,
                'double_bed_quantity' => 0,
                'width'               => 4.0,
                'height'              => 5.0,
                'hourly_price'        => 50000.00,
                'daily_price'         => 300000.00,
            ],
            [
                'name'                => 'Deluxe',
                'code'                => 'DLX',
                'adult_quantity'      => 2,
                'child_quantity'      => 2,
                'single_bed_quantity' => 0,
                'double_bed_quantity' => 1,
                'width'               => 5.0,
                'height'              => 6.0,
                'hourly_price'        => 80000.00,
                'daily_price'         => 500000.00,
            ],
            [
                'name'                => 'Suite',
                'code'                => 'SUT',
                'adult_quantity'      => 4,
                'child_quantity'      => 2,
                'single_bed_quantity' => 2,
                'double_bed_quantity' => 1,
                'width'               => 8.0,
                'height'              => 10.0,
                'hourly_price'        => 150000.00,
                'daily_price'         => 900000.00,
            ],
        ];

        foreach ($roomTypes as $roomType) {
            RoomType::create($roomType);
        }
    }
}
