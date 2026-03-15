<?php

declare(strict_types=1);

namespace App\Actions\Floors;

use App\Models\Floor;
use Exception;

class CreateFloorAction
{
    public function execute(string $name): Floor
    {
        if (Floor::where('name', $name)->exists()) {
            throw new Exception("Tên tầng đã tồn tại");
        }

        $floor = new Floor();
        $floor->name = $name;
        $floor->save();

        return $floor;
    }
}
