<?php

declare(strict_types=1);

namespace App\Actions\ServiceGroups;

use App\Data\ServiceGroupData;
use App\Models\ServiceGroup;

class CreateServiceGroupAction
{
    public function execute(ServiceGroupData $data): ServiceGroup
    {
        $serviceGroup = new ServiceGroup();
        $serviceGroup->service_name = $data->service_name;
        $serviceGroup->save();
        return $serviceGroup;
    }
}
