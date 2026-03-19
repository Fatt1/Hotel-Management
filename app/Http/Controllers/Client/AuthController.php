<?php

namespace App\Http\Controllers\Client;


use App\Actions\Auths\LoginAction;
use App\Actions\Auths\SendOtpEmailAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Actions\Customers\AddCustomerAction;
use App\Data\CustomerData;
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
    public function otp(Request $request)
    {
        $email = (string) ($request->query('email') ?? old('email', ''));

        if ($email === '') {
            return redirect()->route('client.login');
        }

        return view('client.auth.otp', [
            'email' => $email,
        ]);
    }

    public function sendOTP(Request $request, SendOtpEmailAction $action)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $sent = $action->excecute($request->email);

        
        return redirect()
            ->route('client.login.otp', ['email' => $request->email])
            ->with('otp_sent', 'OTP đã được gửi qua email. Vui lòng kiểm tra hộp thư.');
    }

    public function logout(Request $request)
    {
        Auth::guard("customer")->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.home');
    }

    public function login(Request $request, LoginAction $action)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $isValid = $action->excecute($request->email, $request->otp);

        if (! $isValid) {
            return back()
                ->withErrors(['otp' => 'OTP không đúng hoặc đã hết hạn.'])
                ->withInput(['email' => $request->email]);
        }

        return redirect()->route('client.home');
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
