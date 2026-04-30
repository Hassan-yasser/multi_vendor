<?php

namespace App\Providers;

use App\Contracts\Auth\AuthServiceContract;
use App\Contracts\Repositories\Catalog\CategoryRepositoryContract;
use App\Contracts\Repositories\Catalog\SubCategoryRepositoryContract;
use App\Contracts\Repositories\Catalog\SubSubCategoryRepositoryContract;
use App\Contracts\Repositories\DiscountRepositoryContract;
use App\Contracts\Repositories\InventoryRepositoryContract;
use App\Contracts\Repositories\ProductRepositoryContract;
use App\Contracts\Repositories\UserRepositoryContract;
use App\Repositories\Catalog\CategoryRepository;
use App\Repositories\Catalog\SubCategoryRepository;
use App\Repositories\Catalog\SubSubCategoryRepository;
use App\Repositories\DiscountRepository;
use App\Repositories\InventoryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use App\Services\Auth\AuthService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
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

        $this->app->singleton(CategoryRepositoryContract::class, CategoryRepository::class);
        $this->app->singleton(SubCategoryRepositoryContract::class, SubCategoryRepository::class);
        $this->app->singleton(SubSubCategoryRepositoryContract::class, SubSubCategoryRepository::class);

        $this->app->singleton(ProductRepositoryContract::class, ProductRepository::class);
        $this->app->singleton(DiscountRepositoryContract::class, DiscountRepository::class);
        $this->app->singleton(InventoryRepositoryContract::class, InventoryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer('layout.dashboard', function (): void {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            if ($user !== null && $user->store_id !== null) {
                $user->loadMissing('store');
            }
        });

        $limit = ini_get('upload_max_filesize');

        Lang::addLines([
            'validation.uploaded' => 'The file could not be uploaded. Your PHP upload_max_filesize is '.$limit.'. Raise upload_max_filesize and post_max_size (php.ini, public/.htaccess on Apache mod_php, or nginx client_max_body_size).',
        ], 'en');

        Lang::addLines([
            'validation.uploaded' => 'تعذّر رفع الملف. حد PHP الحالي upload_max_filesize = '.$limit.'. زِد upload_max_filesize و post_max_size من php.ini أو إعدادات السيرفر (مثلاً nginx: client_max_body_size).',
        ], 'ar');
    }
}
