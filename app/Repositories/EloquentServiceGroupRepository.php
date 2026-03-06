<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Abstractions\Repositories\ServiceGroupRepository;
use App\Models\ServiceGroup;

class EloquentServiceGroupRepository implements ServiceGroupRepository
{
    public function findById(int $id): ?ServiceGroup
    {
        return ServiceGroup::find($id);
    }

    public function save(ServiceGroup $serviceGroup): bool
    {
        return $serviceGroup->save();
    }

    public function delete(ServiceGroup $serviceGroup): bool
    {
        return $serviceGroup->delete();
    }
}
