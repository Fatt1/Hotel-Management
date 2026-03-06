<?php

namespace App\Http\Controllers\Admin;

use App\Actions\ServiceGroups\CreateServiceGroupAction;
use App\Actions\ServiceGroups\DeleteServiceGroupAction;
use App\Actions\ServiceGroups\GetServiceGroupListAction;
use App\Actions\ServiceGroups\UpdateServiceGroupAction;
use App\Data\ServiceGroupData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceGroupRequest;
use App\Models\ServiceGroup;

class ServiceGroupAdminController extends Controller
{
    /**
     * Danh sách loại dịch vụ
     */
    public function index(GetServiceGroupListAction $action)
    {
        $filters = request()->only(['search']);
        $serviceGroups = $action->executePaginated($filters, perPage: 10);
        return view('admin.service-groups.index', compact('serviceGroups'));
    }

    /**
     * Lưu loại dịch vụ mới
     */
    public function store(ServiceGroupRequest $request, CreateServiceGroupAction $action)
    {
        try {
            $data = ServiceGroupData::from($request->validated());
            $serviceGroup = $action->execute($data);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Loại dịch vụ đã được tạo thành công',
                    'data'    => $serviceGroup,
                ], 201);
            }

            return redirect()
                ->route('admin.service-groups.index')
                ->with('success', 'Loại dịch vụ đã được tạo thành công');
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
     * Cập nhật loại dịch vụ
     */
    public function update(ServiceGroupRequest $request, ServiceGroup $service_group, UpdateServiceGroupAction $action)
    {
        try {
            $data = ServiceGroupData::from($request->validated());
            $serviceGroup = $action->execute($service_group->id, $data);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Loại dịch vụ đã được cập nhật thành công',
                    'data'    => $serviceGroup,
                ], 200);
            }

            return redirect()
                ->route('admin.service-groups.index')
                ->with('success', 'Loại dịch vụ đã được cập nhật thành công');
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
     * Xóa loại dịch vụ
     */
    public function destroy(ServiceGroup $service_group, DeleteServiceGroupAction $action)
    {
        try {
            $action->execute($service_group->id);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Loại dịch vụ đã được xóa thành công',
                ], 200);
            }

            return redirect()
                ->route('admin.service-groups.index')
                ->with('success', 'Loại dịch vụ đã được xóa thành công');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }

            return redirect()
                ->route('admin.service-groups.index')
                ->with('error', $e->getMessage());
        }
    }
}
