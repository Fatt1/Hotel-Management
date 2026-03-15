<?php

declare(strict_types=1);

namespace App\Actions\Services;

use App\Data\ServiceData;
use App\Models\Service;
use Exception;

class UpdateServiceAction
{
    public function execute(int $id, ServiceData $data): Service
    {
        $service = Service::findOrFail($id);

        $service->name = $data->name;
        $service->group_id = $data->group_id;
        $service->unit_price = $data->unit_price;
        $service->unit = $data->unit;
        $service->save();
        return $service;
    }
}
