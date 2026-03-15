<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Staff\AddStaffAction;
use App\Actions\Staff\DeleteStaffAction;
use App\Actions\Staff\GetAllStaffAction;
use App\Actions\Staff\GetStaffByIdAction;
use App\Actions\Staff\ToggleStaffActiveAction;
use App\Actions\Staff\UpdateStaffAction;
use App\Data\StaffData;
use App\Http\Controllers\Controller;
use App\ViewModels\StaffViewModel;
use Exception;
use Illuminate\Http\Request;

class StaffAdminController extends Controller
{
    public function index(Request $request, GetAllStaffAction $getAllStaffAction)
    {
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page', 1);
        $search = $request->input('search', null);
        $role_id = $request->input('role_id', null);
        $sort = $request->input('sort', null);
        
        $staff = $getAllStaffAction->handle($page_size, $page_number, $search, $role_id, $sort);

        return view('admin.staffs.index', [
            'staff' => $staff,
        ]);
    }

    /**
     * Show form tạo mới nhân viên
     */
    public function create()
    {
        $viewModel = new StaffViewModel();
        return view('admin.staffs.form', compact('viewModel'));
    }

    /**
     * Show form cập nhật nhân viên
     */
    public function edit(int $id, GetStaffByIdAction $getStaffByIdAction)
    {
        $staff = $getStaffByIdAction->handle($id);
        if (!$staff) {
            return redirect()->route('admin.staffs.index')->with('error', 'Nhân viên không tồn tại');
        }
        
        $viewModel = new StaffViewModel($staff);
        return view('admin.staffs.form', compact('viewModel'));
    }

    public function show(int $id, GetStaffByIdAction $getStaffByIdAction)
    {
        $staff = $getStaffByIdAction->handle($id);
        if ($staff == null) {
            return response()->json(['message' => 'Nhân viên không tồn tại'], 404);
        }
        return response()->json([
            'id'           => $staff->id,
            'first_name'   => $staff->first_name,
            'last_name'    => $staff->last_name,
            'email'        => $staff->email,
            'phone_number' => $staff->phone_number,
            'role_id'      => $staff->role_id,
            'role_name'    => $staff->role?->name,
            'is_active'    => $staff->is_active,
        ], 200);
    }

    public function store(StaffData $staffData, AddStaffAction $addStaffAction)
    {
        try {
            $staff = $addStaffAction->handle($staffData);
            return redirect()->route('admin.staffs.index')->with('success', 'Nhân viên đã được tạo thành công.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function update(int $id, StaffData $staffData, UpdateStaffAction $updateStaffAction)
    {
        try {
            $updateStaffAction->handle($id, $staffData);
            return redirect()->route('admin.staffs.index')->with('success', 'Cập nhật nhân viên thành công.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(int $id, DeleteStaffAction $deleteStaffAction)
    {
        try {
            $deleteStaffAction->handle($id);
            return response()->json(['message' => 'Xóa nhân viên thành công'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function toggleActive(int $id, ToggleStaffActiveAction $toggleStaffActiveAction)
    {
        try {
            $staff = $toggleStaffActiveAction->handle($id);

            return response()->json([
                'message' => $staff->is_active
                    ? 'Mở khóa tài khoản nhân viên thành công'
                    : 'Khóa tài khoản nhân viên thành công',
                'is_active' => $staff->is_active,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
