<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Enums\UserRoleEnum;
use App\Models\MessageChannel;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('edit-users', function (User $user) {
            return $user->role === UserRoleEnum::ADMIN;
        });

        Gate::define('edit-courses', function (User $user) {
            return $user->role === UserRoleEnum::ADMIN || $user->role === UserRoleEnum::TEACHER;
        });

        Gate::define('assist-lessons', function (User $user) {
            return $user->role === UserRoleEnum::ADMIN || $user->role === UserRoleEnum::TEACHER;
        });
    }
}
