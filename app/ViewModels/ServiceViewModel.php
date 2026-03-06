<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Models\ServiceGroup;
use App\Models\Service;
use Illuminate\Support\Collection;

class ServiceViewModel
{
    private ?Service $service;

    public function __construct(?Service $service = null)
    {
        $this->service = $service;
    }

    /**
     * Trả về service (mới hoặc existing)
     */
    public function service(): Service
    {
        return $this->service ?? new Service();
    }

    /**
     * Danh sách loại dịch vụ để đổ vào dropdown
     */
    public function serviceGroups(): Collection
    {
        return ServiceGroup::select('id', 'service_name')
            ->orderBy('id', 'asc')
            ->get();
    }
}
