<?php

namespace App\Http\Controllers\Admin;

use App\Actions\MaintenanceTickets\CreateMaintenanceTicketAction;
use App\Actions\MaintenanceTickets\DeleteMaintenanceTicketAction;
use App\Actions\MaintenanceTickets\GetMaintenanceTicketByIdAction;
use App\Actions\MaintenanceTickets\GetMaintenanceTicketListAction;
use App\Actions\MaintenanceTickets\UpdateMaintenanceTicketAction;
use App\Data\MaintenanceTicketData;
use App\Http\Controllers\Controller;
use App\Models\MaintenanceTicket;
use App\ViewModels\MaintenanceTicketViewModel;
use Exception;
use Illuminate\Http\Request;

class MaintenanceTicketAdminController extends Controller
{
    public function index(Request $request, GetMaintenanceTicketListAction $action)
    {
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
        ];

        $tickets = $action->executePaginated(filters: $filters, perPage: 7);

        return view('admin.maintenance-tickets.index', [
            'tickets' => $tickets,
        ]);
    }

    public function create()
    {
        $viewModel = new MaintenanceTicketViewModel();

        return view('admin.maintenance-tickets.form', [
            'viewModel' => $viewModel,
        ]);
    }

    public function store(MaintenanceTicketData $data, CreateMaintenanceTicketAction $action)
    {
        try {
            $action->execute($data);

            return redirect()
                ->route('admin.maintenance-tickets.index')
                ->with('success', 'Tạo phiếu sửa chữa thành công.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(int $id, GetMaintenanceTicketByIdAction $action)
    {
        $ticket = $action->execute($id);

        if (!$ticket) {
            return redirect()->route('admin.maintenance-tickets.index')->with('error', 'Phiếu sửa chữa không tồn tại.');
        }

        return view('admin.maintenance-tickets.show', [
            'ticket' => $ticket,
        ]);
    }

    public function edit(MaintenanceTicket $maintenanceTicket)
    {
        $viewModel = new MaintenanceTicketViewModel($maintenanceTicket);

        return view('admin.maintenance-tickets.form', [
            'viewModel' => $viewModel,
        ]);
    }

    public function update(
        MaintenanceTicketData $data,
        MaintenanceTicket $maintenanceTicket,
        UpdateMaintenanceTicketAction $action
    ) {
        try {
            $action->execute($maintenanceTicket, $data);

            return redirect()
                ->route('admin.maintenance-tickets.index')
                ->with('success', 'Cập nhật phiếu sửa chữa thành công.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(MaintenanceTicket $maintenanceTicket, DeleteMaintenanceTicketAction $action)
    {
        try {
            $action->execute($maintenanceTicket);

            return response()->json([
                'success' => true,
                'message' => 'Xóa phiếu sửa chữa thành công.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
