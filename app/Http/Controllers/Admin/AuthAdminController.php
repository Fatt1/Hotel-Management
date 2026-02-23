<?php

namespace App\Http\Controllers\Admin;

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
    public function login(LoginAdminData $data){
        $credentials = [
            'email' => $data->email,
            'password' => $data->password
        ];
        if(Auth::guard('staff')->attempt($credentials)) {
            session()->regenerateToken();
            return redirect()->route('admin.dashboard');
        } else {
            return back()
            ->withErrors(['login_error' => 'Thông tin đăng nhập không chính xác'])
            ->withInput(['email' => $data->email]);
        }
    }
    public function logout(Request $request){
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
