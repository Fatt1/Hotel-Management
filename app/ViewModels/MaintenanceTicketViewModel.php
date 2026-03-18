<?php

namespace App\ViewModels;

use App\Enums\MaintenanceTicketStatus;
use App\Models\Equipment;
use App\Models\MaintenanceTicket;
use App\Models\Room;
use App\Models\Staff;
use Illuminate\Support\Collection;

class MaintenanceTicketViewModel
{
    private ?MaintenanceTicket $ticket;

    public function __construct(MaintenanceTicket $ticket = null)
    {
        $this->ticket = $ticket;
    }

    public function ticket(): MaintenanceTicket
    {
        return $this->ticket ?? new MaintenanceTicket();
    }

    public function rooms(): Collection
    {
        return Room::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function equipments(): Collection
    {
        return Equipment::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function staffs(): Collection
    {
        return Staff::query()
            ->select('id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }

    public function statuses(): array
    {
        return MaintenanceTicketStatus::options();
    }

    public function isEditing(): bool
    {
        return $this->ticket !== null && $this->ticket->exists;
    }

    public function formTitle(): string
    {
        return $this->isEditing() ? 'Chỉnh sửa phiếu sửa chữa' : 'Tạo phiếu sửa chữa mới';
    }

    public function formDescription(): string
    {
        return $this->isEditing()
            ? 'Cập nhật thông tin phiếu sửa chữa bên dưới.'
            : 'Điền thông tin để tạo phiếu sửa chữa thiết bị.';
    }

    public function formAction(): string
    {
        return $this->isEditing()
            ? route('admin.maintenance-tickets.update', $this->ticket->id)
            : route('admin.maintenance-tickets.store');
    }

    public function submitButtonText(): string
    {
        return $this->isEditing() ? 'Lưu thay đổi' : 'Tạo phiếu';
    }
}
