<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Abstractions\Repositories\ServiceRepository;
use App\Models\Service;

class EloquentServiceRepository implements ServiceRepository
{
    public function findById(int $id): ?Service
    {
        return Service::find($id);
    }

    public function save(Service $service): bool
    {
        return $service->save();
    }

    public function delete(Service $service): bool
    {
        return $service->delete();
    }
}
