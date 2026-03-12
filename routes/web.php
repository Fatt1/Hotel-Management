<?php

use App\Http\Controllers\Admin\AuthAdminController;
use App\Http\Controllers\Admin\BookingAdminController;
use App\Http\Controllers\Admin\CustomerAdminController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\RoleAdminController;
use App\Http\Controllers\Admin\StaffAdminController;
use App\Http\Controllers\Admin\RoomDiagramAdminController;
use App\Http\Controllers\Admin\LayoutRoomController;
use App\Http\Controllers\Admin\EquipmentCategoryAdminController;
use App\Http\Controllers\Admin\EquipmentAdminController;
use App\Http\Controllers\Admin\UtilityAdminController;
use App\Http\Controllers\Admin\RoomTypeAdminController;
use App\Http\Controllers\Admin\ServiceGroupAdminController;
use App\Http\Controllers\Admin\ServiceAdminController;
use App\Http\Controllers\Admin\GeneralConfigAdminController;
use App\Http\Controllers\Client\AmenityController;
use App\Http\Controllers\Client\BookingCheckoutController;
use App\Http\Controllers\Client\DiningController;
use App\Http\Controllers\Client\GalleryController;
use App\Http\Controllers\Client\RoomController;
use App\Http\Controllers\RoomAdminController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Client\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('client.home');

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

    // Staff routes
    Route::get("/staffs", [StaffAdminController::class, "index"])->name('admin.staffs.index');
    Route::get("/staffs/create", [StaffAdminController::class, "create"])->name('admin.staffs.create');
    Route::get("/staffs/{id}", [StaffAdminController::class, "show"])->name('admin.staffs.show');
    Route::get("/staffs/{id}/edit", [StaffAdminController::class, "edit"])->name('admin.staffs.edit');
    Route::post("/staffs", [StaffAdminController::class, "store"])->name('admin.staffs.store');
    Route::put("/staffs/{id}", [StaffAdminController::class, "update"])->name('admin.staffs.update');
    Route::delete("/staffs/{id}", [StaffAdminController::class, "destroy"])->name('admin.staffs.destroy');

    // Booking routes
    Route::get("/bookings", [BookingAdminController::class, "index"])->name("admin.bookings.index");
    Route::get('/bookings/create', [BookingAdminController::class,'create'])->name('admin.bookings.create');
    Route::post('/bookings', [BookingAdminController::class,'store'])->name('admin.bookings.store');
    Route::get('bookings/{id}/edit', [BookingAdminController::class, 'edit'])->name('admin.bookings.edit');
    Route::put('bookings/{id}', [BookingAdminController::class, 'update'])->name('admin.bookings.update');
    Route::get('bookings/{id}/checkin', [BookingAdminController::class, 'checkinConfirm'])->name('admin.bookings.checkin');
    Route::post('bookings/{id}/checkin', [BookingAdminController::class, 'checkin'])->name('admin.bookings.checkin.confirm');
    Route::post('bookings/{id}/cancel', [BookingAdminController::class, 'cancel'])->name('admin.bookings.cancel');
    Route::get("bookings/{id}/checkout", [BookingAdminController::class, "checkoutConfirm"])->name('admin.bookings.checkout');
    Route::post("bookings/{id}/checkout", [BookingAdminController::class, "checkout"])->name('admin.bookings.checkout.confirm');
    Route::post('bookings/calculate-payment', [BookingAdminController::class, 'calculatePayment'])->name('admin.bookings.calculate-payment');
    Route::post('bookings/{id}/record-payment', [BookingAdminController::class, 'recordPayment'])->name('admin.bookings.record-payment');
    // Room and service management in booking
    Route::post('bookings/{id}/rooms', [BookingAdminController::class, 'addRoom'])->name('admin.bookings.add-room');
    Route::delete('bookings/{id}/rooms/{roomId}', [BookingAdminController::class, 'removeRoom'])->name('admin.bookings.remove-room');
    Route::put('bookings/{id}/rooms/{roomId}/dates', [BookingAdminController::class, 'updateRoomDates'])->name('admin.bookings.update-room-dates');
    Route::post('bookings/{id}/rooms/{roomId}/services', [BookingAdminController::class, 'addOrUpdateService'])->name('admin.bookings.add-service');
    Route::delete('bookings/{id}/rooms/{roomId}/services/{serviceId}', [BookingAdminController::class, 'removeService'])->name('admin.bookings.remove-service');
    Route::get('bookings/{id}', [BookingAdminController::class, 'show'])->name('admin.bookings.show');
    // Room Type routes
    Route::get('room-types/all', [RoomTypeAdminController::class, 'getAll'])->name('admin.room-types.all');
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

    Route::get('room-diagrams/edit', [RoomDiagramAdminController::class, 'edit'])->name('admin.room-diagrams.edit');
    Route::post('room-diagrams/update', [RoomDiagramAdminController::class, 'update'])->name('admin.room-diagrams.update');
    
    // Layout Room routes
    Route::get('layout-rooms', [LayoutRoomController::class, 'index'])->name('admin.layout-rooms.index');
    
    // Floor API routes
    Route::post('floors', [RoomDiagramAdminController::class, 'storeFloor'])->name('admin.floors.store');
    Route::put('floors/{id}', [RoomDiagramAdminController::class, 'updateFloor'])->name('admin.floors.update');
    Route::delete('floors/{id}', [RoomDiagramAdminController::class, 'destroyFloor'])->name('admin.floors.destroy');
    
    // Room API routes
    Route::post('rooms', [RoomDiagramAdminController::class, 'storeRoom'])->name('admin.rooms.store');
    Route::put('rooms/{id}', [RoomDiagramAdminController::class, 'updateRoom'])->name('admin.rooms.update');
    Route::delete('rooms/{id}', [RoomDiagramAdminController::class, 'destroyRoom'])->name('admin.rooms.destroy');

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
    Route::get('utilities/icons/search', [UtilityAdminController::class, 'searchIcons'])->name('admin.utilities.icons.search');
    Route::resource('utilities', UtilityAdminController::class)->names([
        'index'   => 'admin.utilities.index',
        'create'  => 'admin.utilities.create',
        'store'   => 'admin.utilities.store',
        'show'    => 'admin.utilities.show',
        'edit'    => 'admin.utilities.edit',
        'update'  => 'admin.utilities.update',
        'destroy' => 'admin.utilities.destroy',
    ]);

    // Service Group routes
    Route::resource('service-groups', ServiceGroupAdminController::class)->names([
        'index'   => 'admin.service-groups.index',
        'create'  => 'admin.service-groups.create',
        'store'   => 'admin.service-groups.store',
        'show'    => 'admin.service-groups.show',
        'edit'    => 'admin.service-groups.edit',
        'update'  => 'admin.service-groups.update',
        'destroy' => 'admin.service-groups.destroy',
    ]);

    // Service routes
    Route::get('services/all', [ServiceAdminController::class, 'getAll'])->name('admin.services.all');
    Route::resource('services', ServiceAdminController::class)->names([
        'index'   => 'admin.services.index',
        'create'  => 'admin.services.create',
        'store'   => 'admin.services.store',
        'show'    => 'admin.services.show',
        'edit'    => 'admin.services.edit',
        'update'  => 'admin.services.update',
        'destroy' => 'admin.services.destroy',
    ]);
    
    // General Config routes
    Route::get('general-config', [GeneralConfigAdminController::class, 'index'])->name('admin.general-config.index');
    Route::post('general-config/general', [GeneralConfigAdminController::class, 'updateGeneral'])->name('admin.general-config.update-general');
    Route::post('general-config/surcharge', [GeneralConfigAdminController::class, 'updateSurcharge'])->name('admin.general-config.update-surcharge');
    
    Route::post("/logout", [AuthAdminController::class, "logout"])->name('admin.logout');

    Route::get("/customers", [CustomerAdminController::class, "index"])->name('admin.customers.index');
    Route::get("/customers/create", [CustomerAdminController::class, "create"])->name('admin.customers.create');
    Route::post("/customers", [CustomerAdminController::class, "store"])->name('admin.customers.store');
    Route::get("/customers/email", [CustomerAdminController::class, "getByEmail"])->name('admin.customers.getByEmail');
    Route::get("/customers/{id}", [CustomerAdminController::class, "show"])->name('admin.customers.show');
    Route::get("/customers/{id}/edit", [CustomerAdminController::class, "edit"])->name('admin.customers.edit');
    Route::put("/customers/{id}", [CustomerAdminController::class, "update"])->name('admin.customers.update');
    Route::delete("/customers/{id}", [CustomerAdminController::class, "destroy"])->name('admin.customers.destroy');
});

// =========================================================
// Client Routes
// =========================================================
use App\Http\Controllers\Client\AuthController;

Route::name('client.')->group(function () {
    // Đăng nhập Client
    Route::get('/login', [AuthController::class, 'index'])->name('login');

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

    // Checkout — nhận dữ liệu phòng từ trang rooms và hiển thị form thông tin khách
    Route::post('/booking/checkout', [BookingCheckoutController::class, 'checkout'])->name('booking.checkout');

    // Payment page (Step 3) — receives guest info + booking data from checkout form
    Route::post('/booking/payment', [BookingCheckoutController::class, 'payment'])->name('booking.payment');

    // Confirm — processes payment form, saves to session, redirects to confirmation
    Route::post('/booking/confirm', [BookingCheckoutController::class, 'confirm'])->name('booking.confirm');

    // Confirmation (Step 4) — shows booking confirmed page
    Route::get('/booking/confirmation', [BookingCheckoutController::class, 'confirmation'])->name('booking.confirmation');
});
