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
use App\Http\Controllers\Admin\MaintenanceTicketAdminController;
use App\Http\Controllers\Admin\StatisticsAdminController;
use App\Http\Controllers\Client\AmenityController;
use App\Http\Controllers\Client\BookingCheckoutController;
use App\Http\Controllers\Client\DiningController;
use App\Http\Controllers\Client\GalleryController;
use App\Http\Controllers\Client\RoomController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\ClientBookingController;
use App\Mail\BookingSuccessMail;
use App\Models\Booking;
use App\Http\Controllers\RoomAdminController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Client\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('client.home');

Route::prefix('admin')->middleware("guest:staff")->group(function () {
    Route::get("/login", [AuthAdminController::class, "index"])->name('admin.login');
    Route::post("/login", [AuthAdminController::class, "login"])->name('admin.login.post');
});
Route::prefix('admin')->middleware(["auth:staff", "admin"])->group(function () {
    Route::get("/dashboard", [DashboardAdminController::class, "index"])->middleware('can:dashboard.view')->name('admin.dashboard');
    Route::get("/roles", [RoleAdminController::class, "index"])->middleware('can:roles.view')->name('admin.roles.index');
    Route::get("/roles/{id}", [RoleAdminController::class, "show"])->middleware('can:roles.view')->name('admin.roles.show');
    Route::post("/roles", [RoleAdminController::class, "store"])->middleware('can:roles.create')->name('admin.roles.store');
    Route::put("/roles/{id}", [RoleAdminController::class, "update"])->middleware('can:roles.edit')->name('admin.roles.update');
    Route::delete("/roles/{id}", [RoleAdminController::class, "destroy"])->middleware('can:roles.delete')->name('admin.roles.destroy');
    Route::get('/roles/{id}/permissions', [RoleAdminController::class, 'editPermission'])->middleware('can:roles.view')->name('admin.roles.edit-permission');
    Route::post('/roles/{id}/permissions', [RoleAdminController::class, 'updatePermission'])->middleware('can:roles.edit')->name('admin.roles.update-permissions');

    // Staff routes
    Route::get("/staffs", [StaffAdminController::class, "index"])->middleware('can:staffs.view')->name('admin.staffs.index');
    Route::get("/staffs/create", [StaffAdminController::class, "create"])->middleware('can:staffs.create')->name('admin.staffs.create');
    Route::get("/staffs/{id}", [StaffAdminController::class, "show"])->middleware('can:staffs.view')->name('admin.staffs.show');
    Route::get("/staffs/{id}/edit", [StaffAdminController::class, "edit"])->middleware('can:staffs.edit')->name('admin.staffs.edit');
    Route::post("/staffs", [StaffAdminController::class, "store"])->middleware('can:staffs.create')->name('admin.staffs.store');
    Route::put("/staffs/{id}", [StaffAdminController::class, "update"])->middleware('can:staffs.edit')->name('admin.staffs.update');
    Route::delete("/staffs/{id}", [StaffAdminController::class, "destroy"])->middleware('can:staffs.delete')->name('admin.staffs.destroy');
    Route::patch("/staffs/{id}/toggle-active", [StaffAdminController::class, "toggleActive"])->middleware('can:staffs.edit')->name('admin.staffs.toggle-active');

    // Booking routes
    Route::get("/bookings", [BookingAdminController::class, "index"])->middleware('can:bookings.view')->name("admin.bookings.index");
    Route::get('/bookings/create', [BookingAdminController::class,'create'])->middleware('can:bookings.create')->name('admin.bookings.create');
    Route::post('/bookings', [BookingAdminController::class,'store'])->middleware('can:bookings.create')->name('admin.bookings.store');
    Route::get('bookings/{id}/edit', [BookingAdminController::class, 'edit'])->middleware('can:bookings.edit')->name('admin.bookings.edit');
    Route::put('bookings/{id}', [BookingAdminController::class, 'update'])->middleware('can:bookings.edit')->name('admin.bookings.update');
    Route::get('bookings/{id}/checkin', [BookingAdminController::class, 'checkinConfirm'])->middleware('can:bookings.edit')->name('admin.bookings.checkin');
    Route::post('bookings/{id}/checkin', [BookingAdminController::class, 'checkin'])->middleware('can:bookings.edit')->name('admin.bookings.checkin.confirm');
    Route::post('bookings/{id}/cancel', [BookingAdminController::class, 'cancel'])->middleware('can:bookings.delete')->name('admin.bookings.cancel');
    Route::get("bookings/{id}/checkout", [BookingAdminController::class, "checkoutConfirm"])->middleware('can:bookings.edit')->name('admin.bookings.checkout');
    Route::post("bookings/{id}/checkout", [BookingAdminController::class, "checkout"])->middleware('can:bookings.edit')->name('admin.bookings.checkout.confirm');
    Route::post('bookings/calculate-payment', [BookingAdminController::class, 'calculatePayment'])->middleware('can:bookings.edit')->name('admin.bookings.calculate-payment');
    Route::post('bookings/{id}/record-payment', [BookingAdminController::class, 'recordPayment'])->middleware('can:bookings.edit')->name('admin.bookings.record-payment');
    // Room and service management in booking
    Route::post('bookings/{id}/rooms', [BookingAdminController::class, 'addRoom'])->middleware('can:bookings.edit')->name('admin.bookings.add-room');
    Route::delete('bookings/{id}/rooms/{roomId}', [BookingAdminController::class, 'removeRoom'])->middleware('can:bookings.delete')->name('admin.bookings.remove-room');
    Route::put('bookings/{id}/rooms/{roomId}/dates', [BookingAdminController::class, 'updateRoomDates'])->middleware('can:bookings.edit')->name('admin.bookings.update-room-dates');
    Route::post('bookings/{id}/rooms/{roomId}/services', [BookingAdminController::class, 'addOrUpdateService'])->middleware('can:bookings.edit')->name('admin.bookings.add-service');
    Route::delete('bookings/{id}/rooms/{roomId}/services/{serviceId}', [BookingAdminController::class, 'removeService'])->middleware('can:bookings.delete')->name('admin.bookings.remove-service');
    Route::get('bookings/{id}/invoice', [BookingAdminController::class, 'printInvoice'])->middleware('can:bookings.view')->name('admin.bookings.print-invoice');
    Route::get('bookings/{id}', [BookingAdminController::class, 'show'])->middleware('can:bookings.view')->name('admin.bookings.show');
    // Room Type routes
    Route::get('room-types/all', [RoomTypeAdminController::class, 'getAll'])->middleware('can:settings.view')->name('admin.room-types.all');
    Route::resource('room-types', RoomTypeAdminController::class)->names([
        'index' => 'admin.room-types.index',
        'create' => 'admin.room-types.create',
        'store' => 'admin.room-types.store',
        'show' => 'admin.room-types.show',
        'edit' => 'admin.room-types.edit',
        'update' => 'admin.room-types.update',
        'destroy' => 'admin.room-types.destroy',
    ])
        ->middlewareFor(['index', 'show'], 'can:room_types.view')
        ->middlewareFor(['create', 'store'], 'can:room_types.edit')
        ->middlewareFor(['edit', 'update'], 'can:room_types.edit')
        ->middlewareFor(['destroy'], 'can:room_types.edit');
    
    Route::get('/rooms/available', [RoomAdminController::class, 'getAvailableRooms'])->middleware('can:bookings.view')->name('admin.rooms.available');

    // Room Diagram routes

    Route::get('room-diagrams/edit', [RoomDiagramAdminController::class, 'edit'])->middleware('can:edit_layouts.view')->name('admin.room-diagrams.edit');
    Route::post('room-diagrams/update', [RoomDiagramAdminController::class, 'update'])->middleware('can:edit_layouts.edit')->name('admin.room-diagrams.update');
    
    // Layout Room routes
    Route::get('layout-rooms', [LayoutRoomController::class, 'index'])->middleware('can:layouts.view')->name('admin.layout-rooms.index');
    
    // Floor API routes
    Route::post('floors', [RoomDiagramAdminController::class, 'storeFloor'])->middleware('can:edit_layouts.create')->name('admin.floors.store');
    Route::put('floors/{id}', [RoomDiagramAdminController::class, 'updateFloor'])->middleware('can:edit_layouts.edit')->name('admin.floors.update');
    Route::delete('floors/{id}', [RoomDiagramAdminController::class, 'destroyFloor'])->middleware('can:edit_layouts.delete')->name('admin.floors.destroy');
    
    // Room API routes
    Route::get('rooms/{id}', [RoomDiagramAdminController::class, 'getRoomById'])->middleware('can:layouts.view')->name('admin.rooms.show');
    Route::patch('rooms/{id}/status', [RoomDiagramAdminController::class, 'updateRoomStatus'])->middleware('can:edit_layouts.edit')->name('admin.rooms.update-status');
    Route::put('rooms/{id}/clean', [RoomDiagramAdminController::class, 'cleanRoom'])->middleware('can:edit_layouts.edit')->name('admin.rooms.clean');
    Route::post('rooms', [RoomDiagramAdminController::class, 'storeRoom'])->middleware('can:edit_layouts.create')->name('admin.rooms.store');
    Route::put('rooms/{id}', [RoomDiagramAdminController::class, 'updateRoom'])->middleware('can:edit_layouts.edit')->name('admin.rooms.update');
    Route::delete('rooms/{id}', [RoomDiagramAdminController::class, 'destroyRoom'])->middleware('can:edit_layouts.delete')->name('admin.rooms.destroy');

    // Equipment Category routes
    Route::resource('equipment-categories', EquipmentCategoryAdminController::class)->names([
        'index' => 'admin.equipment-categories.index',
        'create' => 'admin.equipment-categories.create',
        'store' => 'admin.equipment-categories.store',
        'show' => 'admin.equipment-categories.show',
        'edit' => 'admin.equipment-categories.edit',
        'update' => 'admin.equipment-categories.update',
        'destroy' => 'admin.equipment-categories.destroy',
    ])
        ->middlewareFor(['index', 'show'], 'can:equipment_categories.view')
        ->middlewareFor(['create', 'store'], 'can:equipment_categories.create')
        ->middlewareFor(['edit', 'update'], 'can:equipment_categories.edit')
        ->middlewareFor(['destroy'], 'can:equipment_categories.delete');

    // Equipment routes
    Route::resource('equipments', EquipmentAdminController::class)->names([
        'index' => 'admin.equipments.index',
        'create' => 'admin.equipments.create',
        'store' => 'admin.equipments.store',
        'show' => 'admin.equipments.show',
        'edit' => 'admin.equipments.edit',
        'update' => 'admin.equipments.update',
        'destroy' => 'admin.equipments.destroy',
    ])
        ->middlewareFor(['index', 'show'], 'can:equipments.view')
        ->middlewareFor(['create', 'store'], 'can:equipments.create')
        ->middlewareFor(['edit', 'update'], 'can:equipments.edit')
        ->middlewareFor(['destroy'], 'can:equipments.delete');

    // Maintenance Ticket routes
    Route::resource('maintenance-tickets', MaintenanceTicketAdminController::class)->names([
        'index' => 'admin.maintenance-tickets.index',
        'create' => 'admin.maintenance-tickets.create',
        'store' => 'admin.maintenance-tickets.store',
        'show' => 'admin.maintenance-tickets.show',
        'edit' => 'admin.maintenance-tickets.edit',
        'update' => 'admin.maintenance-tickets.update',
        'destroy' => 'admin.maintenance-tickets.destroy',
    ])
        ->middlewareFor(['index', 'show'], 'can:maintenance_tickets.view')
        ->middlewareFor(['create', 'store'], 'can:maintenance_tickets.create')
        ->middlewareFor(['edit', 'update'], 'can:maintenance_tickets.edit')
        ->middlewareFor(['destroy'], 'can:maintenance_tickets.delete');

    // Utility routes
    Route::get('utilities/icons/search', [UtilityAdminController::class, 'searchIcons'])->middleware('can:amenities.view')->name('admin.utilities.icons.search');
    Route::resource('utilities', UtilityAdminController::class)->names([
        'index'   => 'admin.utilities.index',
        'create'  => 'admin.utilities.create',
        'store'   => 'admin.utilities.store',
        'show'    => 'admin.utilities.show',
        'edit'    => 'admin.utilities.edit',
        'update'  => 'admin.utilities.update',
        'destroy' => 'admin.utilities.destroy',
    ])
        ->middlewareFor(['index', 'show'], 'can:amenities.view')
        ->middlewareFor(['create', 'store'], 'can:amenities.create')
        ->middlewareFor(['edit', 'update'], 'can:amenities.edit')
        ->middlewareFor(['destroy'], 'can:amenities.delete');

    // Service Group routes
    Route::resource('service-groups', ServiceGroupAdminController::class)->names([
        'index'   => 'admin.service-groups.index',
        'create'  => 'admin.service-groups.create',
        'store'   => 'admin.service-groups.store',
        'show'    => 'admin.service-groups.show',
        'edit'    => 'admin.service-groups.edit',
        'update'  => 'admin.service-groups.update',
        'destroy' => 'admin.service-groups.destroy',
    ])
        ->middlewareFor(['index', 'show'], 'can:service_categories.view')
        ->middlewareFor(['create', 'store'], 'can:service_categories.create')
        ->middlewareFor(['edit', 'update'], 'can:service_categories.edit')
        ->middlewareFor(['destroy'], 'can:service_categories.delete');

    // Service routes
    Route::get('services/all', [ServiceAdminController::class, 'getAll'])->middleware('can:services.view')->name('admin.services.all');
    Route::resource('services', ServiceAdminController::class)->names([
        'index'   => 'admin.services.index',
        'create'  => 'admin.services.create',
        'store'   => 'admin.services.store',
        'show'    => 'admin.services.show',
        'edit'    => 'admin.services.edit',
        'update'  => 'admin.services.update',
        'destroy' => 'admin.services.destroy',
    ])
        ->middlewareFor(['index', 'show'], 'can:services.view')
        ->middlewareFor(['create', 'store'], 'can:services.create')
        ->middlewareFor(['edit', 'update'], 'can:services.edit')
        ->middlewareFor(['destroy'], 'can:services.delete');
    
    // General Config routes
    Route::get('general-config', [GeneralConfigAdminController::class, 'index'])->middleware('can:settings.view')->name('admin.general-config.index');
    Route::post('general-config/general', [GeneralConfigAdminController::class, 'updateGeneral'])->middleware('can:settings.edit')->name('admin.general-config.update-general');
    Route::post('general-config/surcharge', [GeneralConfigAdminController::class, 'updateSurcharge'])->middleware('can:settings.edit')->name('admin.general-config.update-surcharge');

    // Statistics routes
    Route::prefix('statistics')->name('admin.statistics.')->group(function () {
        Route::get('/', [StatisticsAdminController::class, 'overview'])->middleware('can:statistics.view')->name('index');
        Route::get('/revenue', [StatisticsAdminController::class, 'revenue'])->middleware('can:statistics.view')->name('revenue');
        Route::get('/room-performance', [StatisticsAdminController::class, 'roomPerformance'])->middleware('can:statistics.view')->name('room-performance');
        Route::get('/customers', [StatisticsAdminController::class, 'customers'])->middleware('can:statistics.view')->name('customers');
    });
    
    Route::post("/logout", [AuthAdminController::class, "logout"])->name('admin.logout');

    Route::get("/customers", [CustomerAdminController::class, "index"])->middleware('can:customers.view')->name('admin.customers.index');
    Route::get("/customers/create", [CustomerAdminController::class, "create"])->middleware('can:customers.create')->name('admin.customers.create');
    Route::post("/customers", [CustomerAdminController::class, "store"])->middleware('can:customers.create')->name('admin.customers.store');
    Route::get("/customers/email", [CustomerAdminController::class, "getByEmail"])->middleware('can:customers.view')->name('admin.customers.getByEmail');
    Route::get("/customers/{id}", [CustomerAdminController::class, "show"])->middleware('can:customers.view')->name('admin.customers.show');
    Route::get("/customers/{id}/edit", [CustomerAdminController::class, "edit"])->middleware('can:customers.edit')->name('admin.customers.edit');
    Route::put("/customers/{id}", [CustomerAdminController::class, "update"])->middleware('can:customers.edit')->name('admin.customers.update');
    Route::delete("/customers/{id}", [CustomerAdminController::class, "destroy"])->middleware('can:customers.delete')->name('admin.customers.destroy');
});

// =========================================================
// Client Routes
// =========================================================
use App\Http\Controllers\Client\AuthController;


Route::middleware('auth:customer')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('client.logout');
    Route::get('/profile', [ProfileController::class, 'show'])->name('client.profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('client.profile.update');

    Route::get('/profile/bookings', [ClientBookingController::class, 'index'])->name('client.bookings.index');
    Route::get('/profile/bookings/{id}/details', [ClientBookingController::class, 'details'])->name('client.bookings.details');
    Route::put('/profile/bookings/{id}/dates', [ClientBookingController::class, 'updateDates'])->name('client.bookings.update-dates');
    Route::post('/profile/bookings/{id}/cancel', [ClientBookingController::class, 'cancel'])->name('client.bookings.cancel');
});

Route::name('client.')->group(function () {
    // Đăng nhập Client
    Route::get('/login', [AuthController::class, 'index'])->name('login');

    Route::get('/login/otp', [AuthController::class, 'otp'])->name('login.otp');
    Route::post('/login/send-otp', [AuthController::class, 'sendOTP'])->name('login.send-otp');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegister'])->name('register.store');
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

    // Xác thực email khách hàng để tự điền form checkout. Trả về JSON.
    Route::post('/booking/verify-email', [BookingCheckoutController::class, 'verifyEmail'])->name('booking.verify-email');

    // Payment page (Step 3) — accepts POST (from checkout) and GET (after validation/payment errors)
    Route::match(['get', 'post'], '/booking/payment', [BookingCheckoutController::class, 'payment'])->name('booking.payment');

    // Confirm — processes payment form, saves to DB, redirects to confirmation or MoMo
    Route::post('/booking/confirm', [BookingCheckoutController::class, 'confirm'])->name('booking.confirm');

    // MoMo Return URL
    Route::get('/booking/momo-return', [BookingCheckoutController::class, 'momoReturn'])->name('booking.momo-return');

    // Confirmation (Step 4) — shows booking confirmed page
    Route::get('/booking/confirmation', [BookingCheckoutController::class, 'confirmation'])->name('booking.confirmation');

    // MoMo IPN Webhook (No CSRF via app.php)
    Route::post('/api/payment/momo-ipn', [BookingCheckoutController::class, 'momoIpn']);
});

if (app()->environment('local')) {
    Route::get('/dev/test-booking-mail', function () {
        $booking = Booking::with(['customer', 'bookingDetails.room.roomType'])->latest('id')->first();

        if (!$booking) {
            return response()->json([
                'ok' => false,
                'message' => 'Khong tim thay booking de test mail',
            ], 404);
        }

        $to = request('email', $booking->customer?->email);
        if (!$to) {
            return response()->json([
                'ok' => false,
                'message' => 'Booking khong co email khach, vui long truyen ?email=',
            ], 422);
        }

        Mail::to($to)->queue(new BookingSuccessMail($booking));

        return response()->json([
            'ok' => true,
            'message' => 'Da queue mail test',
            'booking_id' => $booking->id,
            'email' => $to,
        ]);
    })->name('dev.test-booking-mail');
}
