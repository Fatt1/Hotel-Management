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
       $this->app->bind(RoleRepository::class, EloquentRoleRepository::class);
       $this->app->bind(BookingRepository::class, EloquentBookingRepository::class);
       $this->app->bind(CustomerRepository::class, EloquentCustomerRepository::class);
       $this->app->bind(RoomTypeRepository::class, EloquentRoomTypeRepository::class);
       $this->app->bind(EquipmentCategoryRepository::class, EloquentEquipmentCategoryRepository::class);
       $this->app->bind(EquipmentRepository::class, EloquentEquipmentRepository::class);
       $this->app->bind(IStaffRepository::class, EloquentStaffRepository::class);
       $this->app->bind(UtilityRepository::class, EloquentUtilityRepository::class);
       $this->app->bind(FloorRepository::class, EloquentFloorRepository::class);
       $this->app->bind(RoomRepository::class, EloquentRoomRepository::class);
       $this->app->bind(ServiceGroupRepository::class, EloquentServiceGroupRepository::class);
       $this->app->bind(ServiceRepository::class, EloquentServiceRepository::class);
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
