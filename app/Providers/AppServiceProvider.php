<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\User;

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
        Gate::define('audit', function ($_user, $_resource = null) {
            return true;
        });

        Gate::define('restoreAudit', function ($_user, $_resource = null) {
            return true;
        });

        Gate::define('viewPulse', function (User $user) {
            return $user->hasRole('super_admin');
        });
    }
}
