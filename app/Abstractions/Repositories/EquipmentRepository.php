<?php

namespace App\Abstractions\Repositories;

use App\Models\Equipment;

interface EquipmentRepository
{
    public function findById($id): ?Equipment;
    public function save(Equipment $equipment): void;
    public function delete(Equipment $equipment): void;
    public function all(): object;
}
