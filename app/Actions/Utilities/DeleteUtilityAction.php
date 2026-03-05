<?php

namespace App\Actions\Utilities;

use App\Abstractions\Repositories\UtilityRepository;
use App\Models\Utility;
use Exception;

class DeleteUtilityAction
{
    public function __construct(private UtilityRepository $utilityRepository) {}

    public function execute(Utility $utility): void
    {
        $this->utilityRepository->delete($utility);
    }
}
