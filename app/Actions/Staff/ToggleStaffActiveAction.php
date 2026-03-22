<?php

namespace App\Actions\Staff;

use App\Models\Staff;
use Exception;

class ToggleStaffActiveAction
{
    public function handle(int $id): Staff
    {
        $staff = Staff::find($id);

        if (!$staff) {
            throw new Exception('Nhân viên không tồn tại');
        }

        $staff->is_active = !$staff->is_active;
        $staff->save();

        return $staff;
    }
}
