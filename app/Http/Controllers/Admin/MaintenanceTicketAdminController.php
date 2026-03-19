<?php

namespace App\Http\Controllers\Admin;

use App\Actions\MaintenanceTickets\CreateMaintenanceTicketAction;
use App\Actions\MaintenanceTickets\DeleteMaintenanceTicketAction;
use App\Actions\MaintenanceTickets\GetMaintenanceTicketByIdAction;
use App\Actions\MaintenanceTickets\GetMaintenanceTicketListAction;
use App\Actions\MaintenanceTickets\UpdateMaintenanceTicketAction;
use App\Data\MaintenanceTicketData;
use App\Http\Controllers\Controller;
use App\ViewModels\MaintenanceTicketViewModel;
use Exception;
use Illuminate\Http\Request;

class MaintenanceTicketAdminController extends Controller
{
    public function index(Request $request, GetMaintenanceTicketListAction $action)
    {
        $allowedPageSizes = [10, 25, 50, 100];
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
        ];
        $page_size = (int) $request->input('page_size', 10);
        if (!in_array($page_size, $allowedPageSizes, true)) {
            $page_size = 10;
        }

        $tickets = $action->executePaginated(filters: $filters, perPage: $page_size);

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

    public function show(Request $request, int $id, GetMaintenanceTicketByIdAction $action)
    {
        $ticket = $action->execute($id);

        if (!$ticket) {
            return redirect()->route('admin.maintenance-tickets.index')->with('error', 'Phiếu sửa chữa không tồn tại.');
        }

        return view('admin.maintenance-tickets.show', [
            'ticket' => $ticket,
            'returnUrl' => $this->sanitizeReturnUrl($request->query('return_url')),
        ]);
    }

    public function edit(Request $request, int $id, GetMaintenanceTicketByIdAction $action)
    {
        $ticket = $action->execute($id);

        if (!$ticket) {
            return redirect()->route('admin.maintenance-tickets.index')->with('error', 'Phiếu sửa chữa không tồn tại.');
        }

        $viewModel = new MaintenanceTicketViewModel($ticket);

        return view('admin.maintenance-tickets.form', [
            'viewModel' => $viewModel,
            'returnUrl' => $this->sanitizeReturnUrl($request->query('return_url')),
        ]);
    }

    public function update(
        Request $request,
        MaintenanceTicketData $data,
        int $id,
        UpdateMaintenanceTicketAction $action
    ) {
        try {
            $action->execute($id, $data);

            $returnUrl = $this->sanitizeReturnUrl($request->input('return_url'));
            if ($returnUrl !== null) {
                return redirect()
                    ->to($returnUrl)
                    ->with('success', 'Cập nhật phiếu sửa chữa thành công.');
            }

            return redirect()
                ->route('admin.maintenance-tickets.index')
                ->with('success', 'Cập nhật phiếu sửa chữa thành công.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    private function sanitizeReturnUrl(mixed $returnUrl): ?string
    {
        if (!is_string($returnUrl) || $returnUrl === '') {
            return null;
        }

        if (!str_starts_with($returnUrl, url('/admin/maintenance-tickets'))) {
            return null;
        }

        return $returnUrl;
    }

    public function destroy(int $id, DeleteMaintenanceTicketAction $action)
    {
        try {
            $action->execute($id);

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
