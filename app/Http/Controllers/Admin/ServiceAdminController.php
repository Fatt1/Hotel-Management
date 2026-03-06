<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Services\CreateServiceAction;
use App\Actions\Services\DeleteServiceAction;
use App\Actions\Services\GetServiceListAction;
use App\Actions\Services\UpdateServiceAction;
use App\Data\ServiceData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use App\ViewModels\ServiceViewModel;

class ServiceAdminController extends Controller
{
    /**
     * Danh sách dịch vụ
     */
    public function index(GetServiceListAction $action)
    {
        $filters = request()->only(['search', 'group_id']);
        $services = $action->executePaginated($filters, perPage: 10);
        $viewModel = new ServiceViewModel();
        return view('admin.services.index', compact('services', 'viewModel'));
    }

    /**
     * Lưu dịch vụ mới
     */
    public function store(ServiceRequest $request, CreateServiceAction $action)
    {
        try {
            $data = ServiceData::from($request->validated());
            $service = $action->execute($data);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dịch vụ đã được tạo thành công',
                    'data'    => $service->load('group'),
                ], 201);
            }

            return redirect()
                ->route('admin.services.index')
                ->with('success', 'Dịch vụ đã được tạo thành công');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cập nhật dịch vụ
     */
    public function update(ServiceRequest $request, Service $service, UpdateServiceAction $action)
    {
        try {
            $data = ServiceData::from($request->validated());
            $updatedService = $action->execute($service->id, $data);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dịch vụ đã được cập nhật thành công',
                    'data'    => $updatedService->load('group'),
                ], 200);
            }

            return redirect()
                ->route('admin.services.index')
                ->with('success', 'Dịch vụ đã được cập nhật thành công');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Xóa dịch vụ
     */
    public function destroy(Service $service, DeleteServiceAction $action)
    {
        try {
            $action->execute($service->id);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dịch vụ đã được xóa thành công',
                ], 200);
            }

            return redirect()
                ->route('admin.services.index')
                ->with('success', 'Dịch vụ đã được xóa thành công');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }

            return redirect()
                ->route('admin.services.index')
                ->with('error', $e->getMessage());
        }
    }
}
