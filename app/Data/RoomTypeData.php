<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class RoomTypeData extends Data
{
    public function __construct(
        public string $name,
        public string $code,
        public int $adult_quantity,
        public int $child_quantity,
        public int $single_bed_quantity,
        public int $double_bed_quantity,
        public float $width,
        public float $height,
        public float $hourly_price,
        public float $daily_price,
        public ?string $description = null,
    ) {}
}
