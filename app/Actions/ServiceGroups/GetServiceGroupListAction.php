<?php

declare(strict_types=1);

namespace App\Actions\ServiceGroups;

use App\Models\ServiceGroup;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class GetServiceGroupListAction
{
    /**
     * Lấy danh sách loại dịch vụ (không phân trang)
     */
    public function execute(array $filters = []): Collection
    {
        $query = ServiceGroup::query();

        if (!empty($filters['search'])) {
            $query->where('service_name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('id', 'asc')->get();
    }

    /**
     * Lấy danh sách loại dịch vụ với phân trang
     */
    public function executePaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = ServiceGroup::query();

        if (!empty($filters['search'])) {
            $query->where('service_name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('id', 'asc')->paginate($perPage);
    }
}
