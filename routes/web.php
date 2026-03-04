<?php

use App\Http\Controllers\Admin\AuthAdminController;
use App\Http\Controllers\Admin\BookingAdminController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\RoleAdminController;
use App\Http\Controllers\Admin\RoomDiagramAdminController;
use App\Http\Controllers\Admin\EquipmentCategoryAdminController;
use App\Http\Controllers\Admin\EquipmentAdminController;
use App\Http\Controllers\Admin\UtilityAdminController;
use App\Http\Controllers\Admin\RoomTypeAdminController;
use App\Http\Controllers\Client\AmenityController;
use App\Http\Controllers\Client\DiningController;
use App\Http\Controllers\Client\GalleryController;
use App\Http\Controllers\Client\RoomController;
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

    // Equipment Category routes
    Route::resource('equipment-categories', EquipmentCategoryAdminController::class)->names([
        'index' => 'admin.equipment-categories.index',
        'create' => 'admin.equipment-categories.create',
        'store' => 'admin.equipment-categories.store',
        'show' => 'admin.equipment-categories.show',
        'edit' => 'admin.equipment-categories.edit',
        'update' => 'admin.equipment-categories.update',
        'destroy' => 'admin.equipment-categories.destroy',
    ]);

    // Equipment routes
    Route::resource('equipments', EquipmentAdminController::class)->names([
        'index' => 'admin.equipments.index',
        'create' => 'admin.equipments.create',
        'store' => 'admin.equipments.store',
        'show' => 'admin.equipments.show',
        'edit' => 'admin.equipments.edit',
        'update' => 'admin.equipments.update',
        'destroy' => 'admin.equipments.destroy',
    ]);

    // Utility routes
    Route::resource('utilities', UtilityAdminController::class)->names([
        'index' => 'admin.utilities.index',
        'create' => 'admin.utilities.create',
        'store' => 'admin.utilities.store',
        'show' => 'admin.utilities.show',
        'edit' => 'admin.utilities.edit',
        'update' => 'admin.utilities.update',
        'destroy' => 'admin.utilities.destroy',
    ]);
    
    Route::post("/logout", [AuthAdminController::class, "logout"])->name('admin.logout');
});

// =========================================================
// Client Routes
// =========================================================
Route::name('client.')->group(function () {
    // Danh sách loại phòng
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');

    // Chi tiết một loại phòng
    Route::get('/rooms/{id}', [RoomController::class, 'show'])->name('rooms.show');

    // Tiện ích khách sạn
    Route::get('/amenities', [AmenityController::class, 'index'])->name('amenities.index');

    // Nhà hàng & dịch vụ ăn uống
    Route::get('/dinings', [DiningController::class, 'index'])->name('dinings.index');

    // Thư viện ảnh khách sạn
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
});
