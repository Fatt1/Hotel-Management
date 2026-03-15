<?php

namespace App\Providers;

use App\Abstractions\Repositories\BookingRepository;
use App\Abstractions\Repositories\CustomerRepository;
use App\Abstractions\Repositories\FloorRepository;
use App\Abstractions\Repositories\RoleRepository;
use App\Abstractions\Repositories\RoomRepository;
use App\Abstractions\Repositories\RoomTypeRepository;
use App\Abstractions\Repositories\EquipmentCategoryRepository;
use App\Abstractions\Repositories\EquipmentRepository;
use App\Abstractions\Repositories\IStaffRepository;
use App\Abstractions\Repositories\ServiceGroupRepository;
use App\Abstractions\Repositories\ServiceRepository;
use App\Abstractions\Repositories\UtilityRepository;
use App\Models\Staff;
use App\Repositories\EloquentBookingRepository;
use App\Repositories\EloquentCustomerRepository;
use App\Repositories\EloquentFloorRepository;
use App\Repositories\EloquentRoleRepository;
use App\Repositories\EloquentRoomRepository;
use App\Repositories\EloquentRoomTypeRepository;
use App\Repositories\EloquentEquipmentCategoryRepository;
use App\Repositories\EloquentEquipmentRepository;
use App\Repositories\EloquentServiceGroupRepository;
use App\Repositories\EloquentServiceRepository;
use App\Repositories\EloquentStaffRepository;
use App\Repositories\EloquentUtilityRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function($user, $ability) {
            if(!($user instanceof Staff)) {
                return null; // Không can thiệp vào các model khác
            }
            if(!str_contains($ability, ".")) return false;
            [$function, $action] = explode(".", $ability, 2);
            return $user->canAction($function, $action);
        });
    }
}
