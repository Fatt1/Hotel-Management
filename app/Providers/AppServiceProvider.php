<?php

namespace App\Providers;

use App\Abstractions\Repositories\BookingRepository;
use App\Abstractions\Repositories\RoleRepository;
use App\Abstractions\Repositories\RoomTypeRepository;
use App\Models\Staff;
use App\Repositories\EloquentBookingRepository;
use App\Repositories\EloquentRoleRepository;
use App\Repositories\EloquentRoomTypeRepository;
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
       $this->app->bind(RoomTypeRepository::class, EloquentRoomTypeRepository::class);
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
