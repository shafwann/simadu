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

            $targetKategori = 'MASTER';

            // Prioritas 1: Jika ada filter di URL (untuk testing/tamu)
            if (request()->has('instansi')) {
                $targetKategori = request()->query('instansi');
            }
            // Prioritas 2: Jika User Login (Admin/Mahasiswa melihat instansi sendiri)
            elseif (Auth::check()) {
                $user = Auth::user();
                // Jika user punya kategori, pakai itu. Jika tidak, tetap MASTER.
                $targetKategori = $user->kategori_instansi ?? 'MASTER';
            }

            // 2. AMBIL DATA DARI DB (Hanya Read, Jangan Create!)
            $setting = AboutSetting::where('kategori_instansi', $targetKategori)->first();

            // 3. LOGIKA FALLBACK (CADANGAN)
            // Jika data instansi spesifik (misal: DISKOMINFO) belum disetting adminnya (belum ada di DB),
            // Maka TAMPILKAN DATA MASTER dulu agar layout tidak rusak/kosong.
            if (!$setting && $targetKategori !== 'MASTER') {
                $setting = AboutSetting::where('kategori_instansi', 'MASTER')->first();
            }

            // 4. ULTIMATE FALLBACK (Jika MASTER pun belum ada di DB)
            // Buat object dummy di memory saja, jangan simpan ke DB.
            if (!$setting) {
                $setting = new AboutSetting([
                    'app_name' => 'SISTEM MAGANG',
                    'agency_name' => 'PEMERINTAH KABUPATEN',
                    'office_address' => 'Alamat belum disetting',
                    'logo_path' => null,
                ]);
            }

            // 4. Suntikkan variabel $globalAbout ke semua View
            $view->with('globalAbout', $setting);
        });
    }
}
