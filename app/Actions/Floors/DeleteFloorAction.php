<?php

declare(strict_types=1);

namespace App\Actions\Floors;

use App\Models\Floor;
use Exception;

class DeleteFloorAction
{
    public function execute(int $id): void
    {
        $floor = Floor::findOrFail($id);

        if ($floor->rooms()->count() > 0) {
            throw new Exception("Không thể xóa tầng có phòng. Vui lòng xóa hết phòng trước.");
        }

        $floor->delete();
    }
}
