<?php

namespace Database\Seeders;

use App\Models\Floor;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $standard = RoomType::where('code', 'STD')->first();
        $deluxe   = RoomType::where('code', 'DLX')->first();
        $suite    = RoomType::where('code', 'SUT')->first();

        $floor1 = Floor::where('name', 'Tầng 1')->first();
        $floor2 = Floor::where('name', 'Tầng 2')->first();
        $floor3 = Floor::where('name', 'Tầng 3')->first();

        $rooms = [
            // Tầng 1 – Standard
            ['room_type_id' => $standard->id, 'floor_id' => $floor1->id, 'name' => '101', 'status' => 'ready'],
            ['room_type_id' => $standard->id, 'floor_id' => $floor1->id, 'name' => '102', 'status' => 'ready'],
            ['room_type_id' => $standard->id, 'floor_id' => $floor1->id, 'name' => '103', 'status' => 'ready'],

            // Tầng 2 – Deluxe
            ['room_type_id' => $deluxe->id, 'floor_id' => $floor2->id, 'name' => '201', 'status' => 'ready'],
            ['room_type_id' => $deluxe->id, 'floor_id' => $floor2->id, 'name' => '202', 'status' => 'ready'],

            // Tầng 3 – Suite
            ['room_type_id' => $suite->id, 'floor_id' => $floor3->id, 'name' => '301', 'status' => 'ready'],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
