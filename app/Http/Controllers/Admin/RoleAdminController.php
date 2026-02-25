<?php

namespace App\Http\Controllers\Admin;

use App\Actions\GetAllRoleAction;
use App\Actions\GetRoleByIdAction;
use App\Actions\Roles\UpdateRoleClaimAction;
use App\Data\RoleClaimData;
use App\Http\Controllers\Controller;
use App\ViewModels\RoleModelView;
use Exception;
use Illuminate\Http\Request;

class RoleAdminController extends Controller
{
    public function index(Request $request, GetAllRoleAction $getAllRoleAction)
    {
        $page_size = $request->input('page_size', 10);
        $page_number = $request->input('page_number', 1);
        $search = $request->input('search', null);
        $roles = $getAllRoleAction->handle($page_size, $page_number, $search);

        return view('admin.roles.index', [
            'roles' => $roles,
        ]);
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
