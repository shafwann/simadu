<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Models\AboutSetting;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ✅ LOGIKA MULTI-TENANCY VIEW
        // Kode ini akan berjalan di SETIAP halaman untuk menyuntikkan $globalAbout
        View::composer('*', function ($view) {

        $kategori = 'MASTER'; // ✅ DEFAULTNYA SEKARANG 'MASTER' (Global)

        // 1. Jika User Login -> Ambil instansi user tersebut
        if (Auth::check()) {
            // Master tetap melihat MASTER, Admin Instansi melihat Instansinya
            $kategori = Auth::user()->kategori_instansi ?? 'MASTER';
        }
        // 2. Jika Tamu (Guest) -> Cek apakah ada filter di URL (?instansi=DISKOMINFO)
        elseif (request()->has('instansi')) {
            $kategori = request()->query('instansi');
        }

            // 3. Ambil Data Setting dari DB
            // firstOrCreate digunakan agar tidak error jika data belum ada
            $setting = AboutSetting::firstOrCreate(
                ['kategori_instansi' => $kategori],
                [
                    'app_name' => 'System Magang',
                    'agency_name' => 'Pemerintah Kab. Karanganyar',
                    'nama_instansi' => ' system Magang',
                    'jam_operasional' => 'Senin-Jumat: 08.00-16.00'
                ]
            );

            // 4. Suntikkan variabel $globalAbout ke semua View
            $view->with('globalAbout', $setting);
        });
    }
}
