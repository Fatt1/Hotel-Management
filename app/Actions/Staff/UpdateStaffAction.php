<?php

namespace App\Actions\Staff;

use App\Abstractions\Repositories\IStaffRepository;
use App\Data\StaffData;
use App\Models\Staff;

class UpdateStaffAction
{
    public function __construct(
        private IStaffRepository $staffRepository,
    ) {
    }

    public function handle(int $id, StaffData $staffData): Staff
    {
        $staff = $this->staffRepository->findById($id);

        $staff->first_name = $staffData->first_name;
        $staff->last_name = $staffData->last_name;
        $staff->email = $staffData->email;
        $staff->phone_number = $staffData->phone_number;
        $staff->role_id = $staffData->role_id;
        $staff->is_active = $staffData->is_active ?? true;

        if ($staffData->password) {
            $staff->password = $staffData->password;
        }

        $this->staffRepository->save($staff);

        return $staff;
    }
}
