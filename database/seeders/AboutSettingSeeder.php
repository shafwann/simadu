<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AboutSetting;

class AboutSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Gunakan updateOrCreate agar tidak error Duplicate Entry jika dijalankan ulang
        AboutSetting::updateOrCreate(
            // 1. Kunci Pencarian (Cari data dengan kategori ini)
            ['kategori_instansi' => 'BAPPERIDA'],

            // 2. Data yang akan diisi/diupdate
            [
                // BRANDING
                'app_name' => 'SI MADU MONITORING',
                'agency_name' => 'BAPPERIDA KARANGANYAR',
                'app_description' => 'Sistem Informasi Manajemen Magang Badan Perencanaan Pembangunan, Riset, dan Inovasi Daerah.',

                // IDENTITAS INSTANSI
                'nama_instansi' => 'Badan Perencanaan Pembangunan, Riset, dan Inovasi Daerah (BAPPERIDA)',
                'kepala_instansi' => 'Dwi Cahyono, S.Sos., M.Si.',
                'nip_kepala_instansi' => '19730614 199303 1 002',
                'jam_operasional' => 'Senin-Kamis: 07:30-15:00, Jumat: 07:30-11:30',

                // LOKASI (Koordinat Kantor Bapperida)
                'office_name' => 'Kantor Bapperida',
                'office_latitude' => -7.5949176,
                'office_longitude' => 110.9406552,
                'radius_meter' => 500, // Radius absensi

                // JAM MASUK (SETTING PRESENSI)
                'senin_mulai' => '07:00', 'senin_selesai' => '08:00',
                'selasa_mulai' => '07:00', 'selasa_selesai' => '08:00',
                'rabu_mulai' => '07:00', 'rabu_selesai' => '08:00',
                'kamis_mulai' => '07:00', 'kamis_selesai' => '08:00',
                'jumat_mulai' => '07:00', 'jumat_selesai' => '08:00',

                // JAM PULANG (SETTING PRESENSI)
                'senin_checkout_mulai' => '15:00', 'senin_checkout_selesai' => '17:00',
                'selasa_checkout_mulai' => '15:00', 'selasa_checkout_selesai' => '17:00',
                'rabu_checkout_mulai' => '15:00', 'rabu_checkout_selesai' => '17:00',
                'kamis_checkout_mulai' => '15:00', 'kamis_checkout_selesai' => '17:00',
                'jumat_checkout_mulai' => '11:00', 'jumat_checkout_selesai' => '13:00',

                // KONTAK & SOSMED
                'office_address' => 'Komplek Perkantoran Cangakan, Karanganyar',
                'office_phone' => '(0271) 123456',
                'office_email' => 'bapperida@karanganyarkab.go.id',
                'social_instagram' => 'https://instagram.com/bapperida.kra',
                // Biarkan null jika tidak ada
                'social_facebook' => null,
                'social_youtube' => null,
                'social_x' => null,
            ]
        );
    }
}
