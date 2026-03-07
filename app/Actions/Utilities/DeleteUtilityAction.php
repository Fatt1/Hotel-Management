<?php

namespace App\Actions\Utilities;

use App\Models\Utility;

class DeleteUtilityAction
{
    public function execute(Utility $utility): void
    {
        $utility->delete();
    }
}
