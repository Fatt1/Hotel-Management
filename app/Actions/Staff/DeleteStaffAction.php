<?php

namespace App\Actions\Staff;

use App\Abstractions\Repositories\IStaffRepository;
use App\Models\Booking;
use Exception;

class DeleteStaffAction
{
    public function __construct(
        private IStaffRepository $staffRepository,
    ) {
    }

    public function handle(int $id): bool
    {
        // Kiểm tra staff đã tạo booking
        if (Booking::where('staff_id', $id)->exists()) {
            throw new Exception(
                'Không thể xóa nhân viên này vì đã thực hiện các giao dịch đặt phòng'
            );
        }

        $staff = $this->staffRepository->findById($id);

        return $this->staffRepository->delete($staff);
    }
}
