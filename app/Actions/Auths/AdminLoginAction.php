<?php

namespace App\Actions\Auths;

use App\Models\Staff;
use Exception;

class AdminLoginAction
{
    public function execute(string $email, string $password): bool
    {
        $credentials = ['email' => $email, 'password' => $password];
        
        $staff = Staff::where('email', $email)->first();
        
        if(!$staff) {
            throw new Exception ("Tên đăng nhập hoặc mật khẩu không đúng");
        }           
        
        if($staff->is_active == false) {
            throw new Exception ("Tài khoản đã bị khóa");
        }

        if (auth()->guard('staff')->attempt($credentials)) {
            request()->session()->regenerate();
            return true;
        }
        
        return false;

    }
}