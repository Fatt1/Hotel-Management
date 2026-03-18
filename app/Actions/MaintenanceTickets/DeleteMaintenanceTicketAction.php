<?php

namespace App\Actions\MaintenanceTickets;

use App\Models\MaintenanceTicket;

class DeleteMaintenanceTicketAction
{
    public function execute(MaintenanceTicket $ticket): void
    {
        $ticket->delete();
    }
}
