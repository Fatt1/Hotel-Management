<?php

namespace App\Actions\Utilities;

use App\Abstractions\Repositories\UtilityRepository;
use App\Data\UtilityData;
use App\Models\Utility;
use Exception;

class UpdateUtilityAction
{
    public function __construct(private UtilityRepository $utilityRepository) {}

    public function execute(Utility $utility, UtilityData $data): Utility
    {
        $utility->name = $data->name;
        $utility->icon = $data->icon;
        
        $this->utilityRepository->save($utility);
        
        return $utility;
    }
}
