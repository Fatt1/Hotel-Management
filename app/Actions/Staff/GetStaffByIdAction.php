<?php

namespace App\Actions\Staff;

use App\Models\Staff;

class GetStaffByIdAction
{
    public function handle(int $id): ?Staff
    {
        return Staff::find($id);
    }
}
