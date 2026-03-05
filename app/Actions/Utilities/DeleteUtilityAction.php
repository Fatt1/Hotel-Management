<?php

namespace App\Actions\Utilities;

use App\Abstractions\Repositories\UtilityRepository;
use Exception;

class DeleteUtilityAction
{
    public function __construct(private UtilityRepository $utilityRepository) {}

    public function execute(int $utilityId): bool
    {
        $this->utilityRepository->delete($utilityId);
        
        return true;
    }
}
