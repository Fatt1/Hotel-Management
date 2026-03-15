<?php

namespace App\Actions\Utilities;

use App\Data\UtilityData;
use App\Models\Utility;

class UpdateUtilityAction
{
    public function execute(Utility $utility, UtilityData $data): Utility
    {
        $utility->name = $data->name;
        $utility->icon = $data->icon;
        $utility->save();

        return $utility;
    }
}
