<?php

use App\Http\Controllers\Admin\AuthAdminController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\RoleAdminController;
use App\Http\Controllers\Admin\RoomTypeAdminController;
use App\Http\Controllers\Admin\RoomDiagramAdminController;
use App\Http\Controllers\BookingAdminController;
use App\Http\Controllers\RoomAdminController;
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
    Route::get('/bookings/create', [BookingAdminController::class,'create'])->name('admin.bookings.create');
    // Room Type routes
    Route::resource('room-types', RoomTypeAdminController::class)->names([
        'index' => 'admin.room-types.index',
        'create' => 'admin.room-types.create',
        'store' => 'admin.room-types.store',
        'show' => 'admin.room-types.show',
        'edit' => 'admin.room-types.edit',
        'update' => 'admin.room-types.update',
        'destroy' => 'admin.room-types.destroy',
    ]);
    
    Route::get('/rooms/available', [RoomAdminController::class, 'getAvailableRooms'])->name('admin.rooms.available');

    // Room Diagram routes
    Route::get('room-diagrams', [RoomDiagramAdminController::class, 'index'])->name('admin.room-diagrams.index');
    Route::get('room-diagrams/edit', [RoomDiagramAdminController::class, 'edit'])->name('admin.room-diagrams.edit');
    Route::post('room-diagrams/update', [RoomDiagramAdminController::class, 'update'])->name('admin.room-diagrams.update');
    
    Route::post("/logout", [AuthAdminController::class, "logout"])->name('admin.logout');
});
