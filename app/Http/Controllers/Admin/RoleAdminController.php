<?php

namespace App\Http\Controllers\Admin;

use App\Actions\AddRoleAction;
use App\Actions\GetAllRoleAction;
use App\Actions\UpdateRoleAction;
use App\Http\Controllers\Controller;
use App\Data\RoleData;
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

}
