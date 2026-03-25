<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Auths\AdminLoginAction;
use App\Data\LoginAdminData;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthAdminController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }
    public function login(LoginAdminData $data, AdminLoginAction $action)
    {

        try {
            $is_success =  $action->execute($data->email, $data->password);
            if ($is_success) {
                return redirect()->route('admin.layout-rooms.index')->with('success', 'Đăng nhập thành công');
            } else {
                return back()
                    ->withErrors(['login_error' => 'Thông tin đăng nhập không chính xác'])
                    ->withInput(['email' => $data->email]);
            }
        } catch (\Exception $e) {
            return back()
                ->withErrors(['login_error' => $e->getMessage()])
                ->withInput(['email' => $data->email]);
        }
    }
    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
