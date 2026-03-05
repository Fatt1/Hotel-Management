<?php

namespace App\Actions\Utilities;

use App\Models\Utility;

class GetUtilityListAction
{
    public function executePaginated(int $perPage = 10)
    {
        return Utility::paginate($perPage);
    }

    public function getAll()
    {
        return Utility::all();
    }
}
