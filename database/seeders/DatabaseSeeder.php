<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AboutSetting; // Jangan lupa import ini
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        AboutSetting::updateOrCreate(
            ['kategori_instansi' => 'MASTER'],
            [
                'app_name' => 'SISTEM MAGANG TERPADU',
                'agency_name' => 'PEMKAB KARANGANYAR',
                'nama_instansi' => 'Pemerintah Kabupaten Karanganyar',
                'app_description' => 'Portal Resmi Pendaftaran dan Pengelolaan Magang',
                'kepala_instansi' => '-',
                'nip_kepala_instansi' => '-',
                'jam_operasional' => 'Senin-Jumat: 08.00-16.00',
                'radius_meter' => 50,
                // Logo path biarkan null dulu (nanti diupload master)
            ]
        );

        $this->call([
            MasterAdminSeeder::class, // Akun Master
            AdminBidangSeeder::class, // Akun Admin Bapperida (Divisi)
            AboutSettingSeeder::class, // Setting khusus Bapperida
            SheetMahasiswaSeeder::class, // Data Mahasiswa dari sheet

        ]);
    }
}
