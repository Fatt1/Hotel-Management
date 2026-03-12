<?php

namespace App\Actions\Utilities;

use App\Models\Utility;

class GetUtilityListAction
{
    public function executePaginated(int $perPage = 5, ?string $search = null)
    {
        return Utility::query()
            ->when($search, fn($q) => $q->where('name', 'like', '%' . $search . '%'))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getAll()
    {
        return Utility::all();
    }
}
