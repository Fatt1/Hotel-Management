<?php

namespace App\Actions\Staff;

use App\Abstractions\Repositories\IStaffRepository;
use App\Data\StaffData;
use App\Models\Staff;

class AddStaffAction
{
    public function __construct(
        private IStaffRepository $staffRepository,
    ) {
    }

    public function handle(StaffData $staffData): Staff
    {
        $staff = new Staff();
        $staff->first_name = $staffData->first_name;
        $staff->last_name = $staffData->last_name;
        $staff->email = $staffData->email;
        $staff->phone_number = $staffData->phone_number;
        $staff->role_id = $staffData->role_id;
        $staff->password = $staffData->password;
        $staff->is_active = $staffData->is_active ?? true;

        $this->staffRepository->save($staff);

        return $staff;
    }
}
