<?php

namespace Database\Seeders;

use App\Models\Floor;
use Illuminate\Database\Seeder;

class FloorSeeder extends Seeder
{
    public function run(): void
    {
        $floors = [
            ['name' => 'Tầng 1'],
            ['name' => 'Tầng 2'],
            ['name' => 'Tầng 3'],
        ];

        foreach ($floors as $floor) {
            Floor::create($floor);
        }
    }
}
