<?php

use App\Http\Controllers\Admin\AuthAdminController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\RoleAdminController;
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
    Route::get("/roles",[RoleAdminController::class, "index"])->name('admin.roles.index');
    Route::post("/roles",[RoleAdminController::class, "store"])->name('admin.roles.store');
    Route::put("/roles/{id}",[RoleAdminController::class, "update"])->name('admin.roles.update');
    Route::get('/roles/{id}/permissions', [RoleAdminController::class, 'editPermission'])->name('admin.roles.edit-permission');
    Route::post('/roles/{id}/permissions', [RoleAdminController::class, 'updatePermissions'])->name('admin.roles.update-permissions');
    Route::post("/logout",[AuthAdminController::class, "logout"])->name('admin.logout');
});
