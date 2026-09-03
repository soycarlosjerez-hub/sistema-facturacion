<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application telemetry services.
     */
    public function register(): void
    {
        Telescope::night();
    }

    /**
     * Register the application gate for managing telescope entries.
     */
    protected function gate()
    {
        Gate::define('viewTelescope', function (User $user) {
            $allowedRoles = ['admin', 'owner', 'root', 'admin-business'];
            return $user && in_array($user->role, $allowedRoles);
        });
    }

    /**
     * Register any application telemetry services.
     */
    public function boot(): void
    {
        parent::boot();
    }
}
