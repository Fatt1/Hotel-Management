<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class ServiceData extends Data
{
    public function __construct(
        public string $name,
        public int $group_id,
        public float $unit_price,
        public string $unit,
    ) {}
}
