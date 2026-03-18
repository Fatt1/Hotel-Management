<?php

namespace App\Actions\MaintenanceTickets;

use App\Models\MaintenanceTicket;

class GetMaintenanceTicketByIdAction
{
    public function execute(int $id): ?MaintenanceTicket
    {
        return MaintenanceTicket::query()
            ->with(['room.floor', 'equipment.category', 'reportedByStaff.role', 'technician.role'])
            ->find($id);
    }
}
