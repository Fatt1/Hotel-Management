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
        return Utility::create([
            'name' => $data->name,
            'icon' => $data->icon,
        ]);
    }
}
