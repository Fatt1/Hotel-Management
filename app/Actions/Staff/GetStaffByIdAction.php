<?php

namespace App\Actions\Staff;

use App\Abstractions\Repositories\IStaffRepository;
use App\Models\Staff;

class GetStaffByIdAction
{
    public function __construct(
        private IStaffRepository $staffRepository,
    ) {
    }

    public function handle(int $id): ?Staff
    {
        return $this->staffRepository->findById($id);
    }
}
