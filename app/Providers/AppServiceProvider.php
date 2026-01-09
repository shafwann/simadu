<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\AboutSetting;

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
        // Pagination Tailwind
        Paginator::useTailwind();

        // Share data AboutSetting ke semua Blade (footer, dll)
            View::composer('*', function ($view) { $view->with('aboutSetting', AboutSetting::first());});
    }
}
