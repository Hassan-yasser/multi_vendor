<?php

namespace App\Providers;

use App\Contracts\Auth\AuthServiceContract;
use App\Contracts\Repositories\UserRepositoryContract;
use App\Repositories\UserRepository;
use App\Services\Auth\AuthService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(UserRepositoryContract::class, UserRepository::class);
        $this->app->singleton(AuthServiceContract::class, AuthService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
