<?php

namespace App\Providers;

use App\Models\Staff;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Safety net: on production always resolve assets from build manifest.
        if (app()->environment('production')) {
            Vite::useHotFile(storage_path('framework/vite.hot.disabled'));
        }

        Gate::before(function($user, $ability) {
            if(!($user instanceof Staff)) {
                return null; // Không can thiệp vào các model khác
            }
            if(!str_contains($ability, ".")) return null;
            [$function, $action] = explode(".", $ability, 2);
            return $user->canAction($function, $action);
        });
    }
}
