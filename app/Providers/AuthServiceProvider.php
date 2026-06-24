<?php

namespace App\Providers;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //
    ];

    public function boot(): void
    {
        Passport::enablePasswordGrant();

        Passport::tokensCan([
            'admin'          => 'Perform all administrative actions',
            'edit-courses'   => 'Create and edit courses and lessons',
            'assist-lessons' => 'Assist with lessons',
            'lessons:sign'   => 'Sign in and out of lessons',
            'read'           => 'Read-only access',
        ]);

        Passport::tokensExpireIn(now()->addHours(24));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

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
