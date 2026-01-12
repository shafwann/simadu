<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Logbook;

class LogbookSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');

        // Ambil data absensi yang statusnya HADIR atau TELAT saja
        // (Logika: Orang sakit/alpha/ijin tidak bikin logbook)
        $attendances = Attendance::whereIn('status', ['Hadir', 'Telat'])->get();

        foreach ($attendances as $absen) {
            // Kegiatan random ala kantor pemerintahan
            $kegiatan = $faker->randomElement([
                'Membantu rekap data excel pengajuan anggaran tahunan.',
                'Mengikuti apel pagi dan koordinasi rutin divisi.',
                'Menyusun draf laporan bulanan bidang perencanaan.',
                'Melakukan input data usulan masyarakat ke sistem SIPD.',
                'Membantu menyiapkan konsumsi dan ruang rapat untuk tamu daerah.',
                'Memperbaiki format dokumen proposal yang salah ketik.',
                'Melakukan scanning dan digitalisasi arsip surat tahun 2024.',
                'Mengantar surat disposisi ke bagian sekretariat daerah.',
                'Mempelajari alur birokrasi dan administrasi pemerintahan daerah.',
                'Membantu staf senior dalam menyusun slide presentasi PowerPoint.'
            ]);

            Logbook::create([
                'user_id' => $absen->user_id,
                'tanggal' => $absen->tanggal, // Tanggal disamakan dengan data absensi
                'kegiatan' => $kegiatan,

                // ✅ STATUS VALIDASI RANDOM (50% Valid, 50% Pending)
                'is_validated' => $faker->boolean(50),
            ]);
        }
    }
}
