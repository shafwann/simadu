<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Models\AboutSetting;
use Illuminate\Support\Facades\URL; // PENTING
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // PERBAIKAN DI SINI:
        // Kita paksa HTTPS tanpa peduli environment 'local' atau bukan.
        // Ini wajib agar CSS/JS terbaca saat pakai Pinggy/Ngrok.

        // Cek jika URL di .env dimulai dengan https, maka paksa https
        if (str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // ATAU: Kalau mau cara kasar (pasti jalan di ngrok), uncomment baris bawah ini:
        // URL::forceScheme('https');


        // LOGIKA MULTI-TENANCY VIEW
        View::composer('*', function ($view) {

            $targetKategori = 'MASTER';

            // Prioritas 1: Jika ada filter di URL
            if (request()->has('instansi')) {
                $targetKategori = request()->query('instansi');
            }
            // Prioritas 2: Jika User Login
            elseif (Auth::check()) {
                $user = Auth::user();
                $targetKategori = $user->kategori_instansi ?? 'MASTER';
            }

            // 2. AMBIL DATA DARI DB
            // Menggunakan try-catch agar jika tabel belum ada (saat migrate fresh) tidak error
            try {
                $setting = AboutSetting::where('kategori_instansi', $targetKategori)->first();

                // 3. LOGIKA FALLBACK
                if (!$setting && $targetKategori !== 'MASTER') {
                    $setting = AboutSetting::where('kategori_instansi', 'MASTER')->first();
                }
            } catch (\Exception $e) {
                $setting = null;
            }

            // 4. ULTIMATE FALLBACK (Dummy Data)
            if (!$setting) {
                $setting = new AboutSetting([
                    'app_name' => 'SISTEM MAGANG',
                    'agency_name' => 'PEMERINTAH KABUPATEN',
                    'office_address' => 'Alamat belum disetting',
                    'logo_path' => null,
                ]);
            }

            $view->with('globalAbout', $setting);

            Event::listen(Login::class, function ($event) {
                DB::table('authentication_logs')->insert([
                    'user_id'    => $event->user->id,
                    'name'       => $event->user->name,
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent(),
                    'login_at'   => now(),
                ]);
            });
        });
    }
}
