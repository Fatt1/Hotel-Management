<?php

namespace App\Actions\MaintenanceTickets;

use App\Data\MaintenanceTicketData;
use App\Models\MaintenanceTicket;
use RuntimeException;

class CreateMaintenanceTicketAction
{
    public function execute(MaintenanceTicketData $data): MaintenanceTicket
    {
        $reporterId = auth('staff')->id();
        if (!$reporterId) {
            throw new RuntimeException('Không xác định được nhân viên báo cáo. Vui lòng đăng nhập lại.');
        }

        $ticket = new MaintenanceTicket();
        $ticket->room_id = $data->room_id;
        $ticket->equipment_id = $data->equipment_id;
        $ticket->reported_date = now()->toDateString();
        $ticket->issue_description = $data->issue_description;
        $ticket->technician_note = $data->technician_note;
        $ticket->status = $data->status;
        $ticket->repair_cost = $data->repair_cost;
        $ticket->reported_by_staff_id = (int) $reporterId;
        $ticket->technician_id = $data->technician_id;

        $ticket->save();

        return $ticket;
    }
}
