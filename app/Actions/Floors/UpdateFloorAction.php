<?php

declare(strict_types=1);

namespace App\Actions\Floors;

use App\Models\Floor;
use Exception;

class UpdateFloorAction
{
    public function execute(int $id, string $name): Floor
    {
        $floor = Floor::findOrFail($id);

        if (Floor::where('name', $name)->where('id', '!=', $id)->exists()) {
            throw new Exception("Tên tầng đã tồn tại");
        }

        $floor->name = $name;
        $floor->save();

        return $floor;
    }
}
