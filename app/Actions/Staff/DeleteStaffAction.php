<?php

namespace App\Actions\Staff;

use App\Models\Booking;
use App\Models\Staff;
use Exception;

class DeleteStaffAction
{
    public function handle(int $id): bool
    {
        if (Booking::where('staff_id', $id)->exists()) {
            throw new Exception(
                'Không thể xóa nhân viên này vì đã thực hiện các giao dịch đặt phòng'
            );
        }

        $staff = Staff::findOrFail($id);
        return $staff->delete();
    }
}
