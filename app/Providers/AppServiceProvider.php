<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
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
        Gate::define('access-patient-services', fn (User $user): bool => $user->hasRole(UserRole::Patient));
        Gate::define('manage-appointments', fn (User $user): bool => $user->hasRole(UserRole::Admin, UserRole::Doctor));
        Gate::define('manage-pharmacy-orders', fn (User $user): bool => $user->hasRole(UserRole::Admin, UserRole::PharmacyAdmin));
    }
}
