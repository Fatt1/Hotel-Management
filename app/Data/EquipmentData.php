<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class EquipmentData extends Data
{
    public function __construct(
        public string $name,
        public int $equipment_category_id,
        public ?float $import_price = null,
    ) {}
}
