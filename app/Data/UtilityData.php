<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class UtilityData extends Data
{
    public function __construct(
        public string $name,
        public ?string $icon,
    ) {}
}
