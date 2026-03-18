<?php

namespace App\Actions\MaintenanceTickets;

use App\Models\MaintenanceTicket;

class GetMaintenanceTicketListAction
{
    public function executePaginated(array $filters = [], int $perPage = 10)
    {
        $query = MaintenanceTicket::query()
            ->with(['room', 'equipment', 'technician'])
            ->orderByDesc('id');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('issue_description', 'like', "%{$search}%")
                    ->orWhereHas('room', function ($roomQuery) use ($search) {
                        $roomQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('equipment', function ($equipmentQuery) use ($search) {
                        $equipmentQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }
}
