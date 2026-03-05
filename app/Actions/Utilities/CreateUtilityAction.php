<?php

namespace App\Actions\Utilities;

use App\Abstractions\Repositories\UtilityRepository;
use App\Data\UtilityData;
use App\Models\Utility;

class CreateUtilityAction
{
    public function __construct(private UtilityRepository $utilityRepository) {}

    public function execute(UtilityData $data): Utility
    {
        $utility = new Utility();
        $utility->name = $data->name;
        $utility->icon = $data->icon;
        
        $this->utilityRepository->save($utility);
        
        return $utility;
    }
}
