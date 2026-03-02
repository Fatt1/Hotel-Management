<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Roles\AddRoleAction;
use App\Actions\Roles\DeleteRoleAction;
use App\Actions\Roles\GetAllRoleAction;
use App\Actions\Roles\GetRoleByIdAction;
use App\Actions\Roles\UpdateRoleAction;
use App\Actions\Roles\UpdateRoleClaimAction;
use App\Data\RoleClaimData;
use App\Data\RoleData;
use App\Http\Controllers\Controller;
use App\ViewModels\RoleModelView;
use Exception;
use Illuminate\Http\Request;

class RoleAdminController extends Controller
{
    public function index(Request $request, GetAllRoleAction $getAllRoleAction)
    {
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page', 1);
        $search = $request->input('search', null);
        $roles = $getAllRoleAction->handle($page_size, $page_number, $search);
        
        return view('admin.roles.index', [
            'roles' => $roles,
        ]);
    }

    public function show(int $id, GetRoleByIdAction $getRoleByIdAction) {
        $role = $getRoleByIdAction->handle($id);
        if($role == null) {
            return response()->json(['message' => 'Vai trò không tồn tại'], 404);
        }
        $roleData =new RoleData($role->name, $role->id);
        return response()->json($roleData,200);
    }

    public function update(int $id, RoleData $roleData, UpdateRoleAction $updateRoleAction) {
        try {
            $updateRoleAction->handle($id, $roleData);
            return response()->json(['message' => 'Cập nhật vai trò thành công'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    public function destroy(int $id, DeleteRoleAction $deleteRoleAction) {
        try{
            $deleteRoleAction->handle($id);
            return response()->json(['message' => 'Xóa vai trò thành công'], 200);
        }
        catch (Exception $e) {
            return response()->json(['message'=> $e->getMessage()], 404);
        }
    }

    public function store(RoleData $roleData, AddRoleAction $addRoleAction){
        $role = $addRoleAction->handle($roleData);
        return redirect()->route('admin.roles.index')->with('success','Vai trò đã được tạo thành công.');
        
    }
    public function editPermission(int $id, GetRoleByIdAction $getRoleByIdAction)
    {
        $role = $getRoleByIdAction->handle($id);
        $roleViewModel = new RoleModelView($role);
        return view('admin.roles.edit-permission', [
            'role' => $roleViewModel,
        ]);
    }

    
    public function updatePermission(int $id, Request $request, UpdateRoleClaimAction $updateRoleClaimAction)
    {
        $claimData = $request->input('claims', []);
        $claims = array_map(function ($claim) {
            return new RoleClaimData(
                role_id: $claim['role_id'],
                claim_name: $claim['claim_name'],
                claim_value: $claim['claim_value']
            );
        }, $claimData);
        try {
            $updateRoleClaimAction->handle($id, ...$claims);
            return response()->json(['message' => 'Cập nhật quyền thành công']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
