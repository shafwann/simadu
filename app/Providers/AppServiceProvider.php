<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\AboutSetting;
use Illuminate\Support\Facades\Schema;

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
        // 1. Set Pagination menggunakan Tailwind
        Paginator::useTailwind();

        // 2. Share data AboutSetting ke semua View (Navbar, Footer, dll)
        // Cek dulu apakah tabel ada agar tidak error saat 'php artisan migrate:fresh'
        if (Schema::hasTable('about_settings')) {

            $globalAbout = AboutSetting::first();

            // Share dengan nama variabel '$globalAbout' (untuk branding baru)
            View::share('globalAbout', $globalAbout);

            // Opsional: Jika ada view lama yang menggunakan '$aboutSetting', uncomment baris bawah ini:
            // View::share('aboutSetting', $globalAbout);
        }
    }
}
