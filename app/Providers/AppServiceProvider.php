<?php

namespace App\Providers;

use App\Services\AuthService;
use App\Services\CategoryPlanService;
use App\Services\CategoryServices;
use App\Services\MonthPlanService;
use App\Services\TransactionServices;
use App\Services\UserServices;
use App\Services\WalletServices;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthService::class);
        $this->app->bind(UserServices::class);
        $this->app->bind(WalletServices::class);
        $this->app->bind(CategoryServices::class);
        $this->app->bind(MonthPlanService::class);
        $this->app->bind(CategoryPlanService::class);
        $this->app->bind(TransactionServices::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
