<?php

namespace App\Actions\MaintenanceTickets;

use App\Data\MaintenanceTicketData;
use App\Models\MaintenanceTicket;

class UpdateMaintenanceTicketAction
{
    public function execute(int $id, MaintenanceTicketData $data): MaintenanceTicket
    {
        $ticket = MaintenanceTicket::query()->findOrFail($id);

        $ticket->room_id = $data->room_id;
        $ticket->equipment_id = $data->equipment_id;
        $ticket->issue_description = $data->issue_description;
        $ticket->technician_note = $data->technician_note;
        $ticket->status = $data->status;
        $ticket->repair_cost = $data->repair_cost;
        $ticket->technician_id = $data->technician_id;

        $ticket->save();

        return $ticket;
    }
}
