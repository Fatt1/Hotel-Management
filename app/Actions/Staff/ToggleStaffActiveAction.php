<?php

namespace App\Actions\Staff;

use App\Abstractions\Repositories\IStaffRepository;
use App\Models\Staff;
use Exception;

class ToggleStaffActiveAction
{
    public function __construct(
        private IStaffRepository $staffRepository,
    ) {
    }

    public function handle(int $id): Staff
    {
        $staff = $this->staffRepository->findById($id);

        if (!$staff) {
            throw new Exception('Nhân viên không tồn tại');
        }

        $staff->is_active = !$staff->is_active;
        $this->staffRepository->save($staff);

        return $staff;
    }
}
