<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Show the client login form (Passwordless / OTP)
     */
    public function index()
    {
        return view('client.auth.login');
    }
}
