<?php

namespace App\Http\Controllers\Admin;

use App\Actions\GetAllRoleAction;
use App\Actions\GetRoleByIdAction;
use App\Http\Controllers\Controller;
use App\ViewModels\RoleModelView;
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

}
