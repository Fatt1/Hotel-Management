<?php

namespace App\Actions\Utilities;

use App\Data\UtilityData;
use App\Models\Utility;

class CreateUtilityAction
{
    public function execute(UtilityData $data): Utility
    {
        $utility = new Utility();
        $utility->name = $data->name;
        $utility->icon = $data->icon;
        $utility->save();

        return $utility;
    }
}
