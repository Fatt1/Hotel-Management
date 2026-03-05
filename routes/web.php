<?php

use App\Http\Controllers\Admin\AuthAdminController;
use App\Http\Controllers\Admin\CustomerAdminController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\RoleAdminController;
use App\Http\Controllers\BookingAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->middleware("guest:staff")->group(function () {
    Route::get("/login", [AuthAdminController::class, "index"])->name('admin.login');
    Route::post("/login", [AuthAdminController::class, "login"])->name('admin.login.post');
});
Route::prefix('admin')->middleware(["auth:staff", "admin"])->group(function () {
    Route::get("/dashboard", [DashboardAdminController::class, "index"])->name('admin.dashboard');
    Route::get("/roles", [RoleAdminController::class, "index"])->name('admin.roles.index');
    Route::get("/roles/{id}", [RoleAdminController::class, "show"])->name('admin.roles.show');
    Route::post("/roles", [RoleAdminController::class, "store"])->name('admin.roles.store');
    Route::put("/roles/{id}", [RoleAdminController::class, "update"])->name('admin.roles.update');
    Route::delete("/roles/{id}", [RoleAdminController::class, "destroy"])->name('admin.roles.destroy');
    Route::get('/roles/{id}/permissions', [RoleAdminController::class, 'editPermission'])->name('admin.roles.edit-permission');
    Route::post('/roles/{id}/permissions', [RoleAdminController::class, 'updatePermission'])->name('admin.roles.update-permissions');

    // Booking routes
    Route::get("/bookings", [BookingAdminController::class, "index"])->name("admin.bookings.index");
    Route::post("/logout", [AuthAdminController::class, "logout"])->name('admin.logout');
    Route::get("/customers", [CustomerAdminController::class, "index"])->name('admin.customers.index');
    
});
