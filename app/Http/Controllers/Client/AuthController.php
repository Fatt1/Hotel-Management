<?php

namespace App\Http\Controllers\Client;

use App\Actions\Customers\AddCustomerAction;
use App\Data\CustomerData;
use App\Http\Controllers\Controller;
use App\ViewModels\ClientAuthViewModel;
use Exception;

class AuthController extends Controller
{
    /**
     * Show the client login form (Passwordless / OTP)
     */
    public function index()
    {
        return view('client.auth.login');
    }

    /**
     * Show the client register form.
     */
    public function register()
    {
        $viewModel = new ClientAuthViewModel();
        return view('client.auth.register', [
            'viewModel' => $viewModel,
        ]);
    }

    /**
     * Handle client registration.
     */
    public function storeRegister(CustomerData $data, AddCustomerAction $action)
    {
        try {
            $action->handle($data);

            return redirect()
                ->route('client.login')
                ->with('success', 'Đăng ký thành công. Bạn có thể đăng nhập bằng email vừa tạo.');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Không thể đăng ký tài khoản. Vui lòng thử lại.');
        }
    }
}
