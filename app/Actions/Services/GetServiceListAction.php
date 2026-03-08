<?php

declare(strict_types=1);

namespace App\Actions\Services;

use App\Models\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class GetServiceListAction
{
    /**
     * Lấy danh sách dịch vụ với phân trang
     * Query Action: Dùng Eloquent trực tiếp theo CQRS rule
     */
    public function execute(): Collection
    {
        return Service::query()->with('group')->orderBy('id', 'asc')->get();
    }

    public function executePaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Service::query()->with('group');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        return $query->orderBy('id', 'asc')->paginate($perPage);
    }
}
