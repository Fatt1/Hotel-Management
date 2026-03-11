<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Models\Floor;
use Illuminate\Support\Collection;

class LayoutRoomIndexViewModel
{
    public array $statusCounts;
    public Collection $floors;
    public string $filterDate;
    public ?string $filterStatus;
    public string $groupBy;

    private Collection $allRooms;

    public function __construct(
        Collection $allRooms,
        string $filterDate,
        ?string $filterStatus = null,
        string $groupBy = 'type'
    ) {
        $this->allRooms = $allRooms;
        $this->filterDate = $filterDate;
        $this->filterStatus = $filterStatus;
        $this->groupBy = $groupBy;
        $this->statusCounts = $this->computeStatusCounts();
        $this->floors = Floor::orderBy('name')->get(['id', 'name']);
    }

    private function computeStatusCounts(): array
    {
        $counts = [
            'available'    => 0,
            'reserved'     => 0,
            'arriving'     => 0,
            'occupied'     => 0,
            'late_checkout' => 0,
            'dirty'        => 0,
        ];

        foreach ($this->allRooms as $room) {
            $counts[$room->status->value]++;
        }

        return $counts;
    }

    public function getFilteredRooms(): Collection
    {
        $collection = $this->allRooms;

        if ($this->filterStatus && $this->filterStatus !== 'all') {
            $collection = $collection->filter(
                fn($room) => $room->status->value === $this->filterStatus
            );
        }

        return match ($this->groupBy) {
            'floor' => $collection->groupBy('floorName'),
            'room'  => $collection->groupBy(fn($r) => 'Tất cả phòng'),
            default => $collection->groupBy('roomTypeCode'),
        };
    }

    public function getTotalRooms(): int
    {
        return array_sum($this->statusCounts);
    }

    public function isStatusActive(string $status): bool
    {
        return $this->filterStatus === $status || (!$this->filterStatus && $status === 'all');
    }

    public function getStatusFilterUrl(string $status): string
    {
        $params = [
            'date'     => $this->filterDate,
            'status'   => $status === 'all' ? null : $status,
            'group_by' => $this->groupBy,
        ];

        return route('admin.layout-rooms.index', array_filter($params));
    }

    public function getGroupByUrl(string $groupBy): string
    {
        $params = [
            'date'     => $this->filterDate,
            'status'   => $this->filterStatus === 'all' ? null : $this->filterStatus,
            'group_by' => $groupBy,
        ];

        return route('admin.layout-rooms.index', array_filter($params));
    }

    public function isGroupByActive(string $groupBy): bool
    {
        return $this->groupBy === $groupBy;
    }
}
