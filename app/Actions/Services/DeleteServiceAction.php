<?php

declare(strict_types=1);

namespace App\Actions\Services;

use App\Models\Service;
use Exception;

class DeleteServiceAction
{
    public function execute(int $id): void
    {
        $service = Service::findOrFail($id);
        $service->delete();
    }
}
