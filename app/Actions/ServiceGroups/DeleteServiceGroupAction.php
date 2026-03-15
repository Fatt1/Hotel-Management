<?php

declare(strict_types=1);

namespace App\Actions\ServiceGroups;

use App\Models\Service;
use App\Models\ServiceGroup;
use Exception;

class DeleteServiceGroupAction
{
    public function execute(int $id): void
    {
        $serviceGroup = ServiceGroup::findOrFail($id);

        $serviceCount = Service::where('group_id', $id)->count();

        if ($serviceCount > 0) {
            throw new Exception(
                "Không thể xóa loại dịch vụ này vì đang có {$serviceCount} dịch vụ thuộc loại này"
            );
        }

        $serviceGroup->delete();
    }
}
