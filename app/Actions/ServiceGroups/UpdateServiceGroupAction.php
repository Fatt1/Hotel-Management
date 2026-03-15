<?php

declare(strict_types=1);

namespace App\Actions\ServiceGroups;

use App\Data\ServiceGroupData;
use App\Models\ServiceGroup;
use Exception;

class UpdateServiceGroupAction
{
    public function execute(int $id, ServiceGroupData $data): ServiceGroup
    {
        $serviceGroup = ServiceGroup::findOrFail($id);
        $serviceGroup->service_name = $data->service_name;
        $serviceGroup->save();
        return $serviceGroup;
    }
}
