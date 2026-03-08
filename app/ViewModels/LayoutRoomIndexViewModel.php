<?php

declare(strict_types=1);

namespace App\ViewModels;

use Illuminate\Support\Collection;

class LayoutRoomIndexViewModel
{
    /**
     * Grouped rooms by type/floor/room
     * @var Collection<string, Collection<RoomLayoutViewModel>>
     */
    public Collection $roomsByType;
    
    /**
     * Status counts
     * @var array<string, int>
     */
    public array $statusCounts;
    
    /**
     * Filter date (Y-m-d)
     */
    public string $filterDate;
    
    /**
     * Current status filter
     */
    public ?string $filterStatus;
    
    /**
     * Layout grouping mode: 'type', 'floor', 'room'
     */
    public string $groupBy;
    
    /**
     * Floors collection for grouping selector
     * @var Collection
     */
    public Collection $floors;

    public function __construct(
        Collection $roomsByType,
        array $statusCounts,
        string $filterDate,
        ?string $filterStatus = null,
        string $groupBy = 'type',
        Collection $floors = null
    ) {
        $this->roomsByType = $roomsByType;
        $this->statusCounts = $statusCounts;
        $this->filterDate = $filterDate;
        $this->filterStatus = $filterStatus;
        $this->groupBy = $groupBy;
        $this->floors = $floors ?? collect();
    }

    /**
     * Get filtered rooms based on current status filter
     */
    public function getFilteredRooms(): Collection
    {
        if (!$this->filterStatus || $this->filterStatus === 'all') {
            return $this->roomsByType;
        }

        return $this->roomsByType->map(function ($rooms) {
            return $rooms->filter(function ($room) {
                return $room->status->value === $this->filterStatus;
            });
        })->filter(function ($rooms) {
            return $rooms->isNotEmpty();
        });
    }

    /**
     * Get total room count
     */
    public function getTotalRooms(): int
    {
        return array_sum($this->statusCounts);
    }

    /**
     * Check if a specific status filter is active
     */
    public function isStatusActive(string $status): bool
    {
        return $this->filterStatus === $status || (!$this->filterStatus && $status === 'all');
    }

    /**
     * Get URL for status filter
     */
    public function getStatusFilterUrl(string $status): string
    {
        $params = [
            'date' => $this->filterDate,
            'status' => $status === 'all' ? null : $status,
            'group_by' => $this->groupBy,
        ];

        return route('admin.layout-rooms.index', array_filter($params));
    }

    /**
     * Get URL for group by change
     */
    public function getGroupByUrl(string $groupBy): string
    {
        $params = [
            'date' => $this->filterDate,
            'status' => $this->filterStatus === 'all' ? null : $this->filterStatus,
            'group_by' => $groupBy,
        ];

        return route('admin.layout-rooms.index', array_filter($params));
    }

    /**
     * Check if a specific group by is active
     */
    public function isGroupByActive(string $groupBy): bool
    {
        return $this->groupBy === $groupBy;
    }
}
