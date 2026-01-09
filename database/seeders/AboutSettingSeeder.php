<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AboutSetting;

class AboutSettingSeeder extends Seeder
{
    public function run(): void
    {
        AboutSetting::firstOrCreate([], [
            'nama_instansi' => 'Badan Perencanaan Pembangunan, Riset, dan Inovasi Daerah (BAPPERIDA)',
            'kepala_instansi' => 'Dwi Cahyono, S.Sos., M.Si.',
            'nip_kepala_instansi' => '19730614 199303 1 002',
            'jam_operasional' => 'Senin 07:30-15:00, Selasa 07:30-15:00, Rabu 07:30-15:00, Kamis 07:30-15:00, Jumat 07:30-11:30',

            // LOKASI
            'office_latitude' => -7.5949176,
            'office_longitude' => 110.9406552,
            'radius_meter' => 500,

            // CHECK-IN
            'senin_mulai' => '07:00',
            'senin_selesai' => '08:00',
            'selasa_mulai' => '07:00',
            'selasa_selesai' => '08:00',
            'rabu_mulai' => '07:00',
            'rabu_selesai' => '08:00',
            'kamis_mulai' => '07:00',
            'kamis_selesai' => '08:00',
            'jumat_mulai' => '07:00',
            'jumat_selesai' => '08:00',

            // CHECK-OUT
            'senin_checkout_mulai' => '15:00',
            'senin_checkout_selesai' => '16:00',
            'selasa_checkout_mulai' => '15:00',
            'selasa_checkout_selesai' => '16:00',
            'rabu_checkout_mulai' => '15:00',
            'rabu_checkout_selesai' => '16:00',
            'kamis_checkout_mulai' => '15:00',
            'kamis_checkout_selesai' => '16:00',
            'jumat_checkout_mulai' => '15:00',
            'jumat_checkout_selesai' => '16:00',
        ]);
    }
}
