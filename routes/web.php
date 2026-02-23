<?php

use App\Http\Controllers\Admin\AuthAdminController;
use App\Http\Controllers\Admin\DashboardAdminController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->middleware("guest:staff")->group(function () {
    Route::get("/login",[AuthAdminController::class, "index"])->name('admin.login');
    Route::post("/login",[AuthAdminController::class, "login"])->name('admin.login.post');
});
Route::prefix('admin')->middleware(["auth:staff", "admin"])->group(function () {
    Route::get("/dashboard",[DashboardAdminController::class, "index"])->name('admin.dashboard');
    Route::post("/logout",[AuthAdminController::class, "logout"])->name('admin.logout');
});
