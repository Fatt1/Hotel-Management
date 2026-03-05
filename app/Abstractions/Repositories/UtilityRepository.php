<?php

namespace App\Abstractions\Repositories;

interface UtilityRepository
{
    public function findById(int $id);

    public function save(array $data);

    public function delete(int $id);

    public function all();
}
