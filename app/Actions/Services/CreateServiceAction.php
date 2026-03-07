<?php

declare(strict_types=1);

namespace App\Actions\Services;

use App\Data\ServiceData;
use App\Models\Service;

class CreateServiceAction
{
    public function execute(ServiceData $data): Service
    {
        $service = new Service();
        $service->name = $data->name;
        $service->group_id = $data->group_id;
        $service->unit_price = $data->unit_price;
        $service->unit = $data->unit;
        $service->save();
        return $service;
    }
}
