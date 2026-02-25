<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class RoleClaimData extends Data
{
    public function __construct(
        public int $role_id,
        public string $claim_name,
        public string $claim_value,
    ) {
    }
}
