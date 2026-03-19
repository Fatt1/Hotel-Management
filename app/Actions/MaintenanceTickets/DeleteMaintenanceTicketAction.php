<?php

namespace App\Actions\MaintenanceTickets;

use App\Models\MaintenanceTicket;

class DeleteMaintenanceTicketAction
{
    public function execute(int $id): void
    {
        $ticket = MaintenanceTicket::query()->findOrFail($id);

        $ticket->delete();
    }
}
