<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');

        // Ambil semua mahasiswa
        $students = User::where('role', 'mahasiswa')->get();

        foreach ($students as $student) {
            // Kita buatkan data untuk 15 hari kerja pertama sejak tanggal masuk
            $startDate = Carbon::parse($student->tanggal_masuk);

            for ($i = 0; $i < 15; $i++) {
                $date = $startDate->copy()->addDays($i);

                // Skip Sabtu & Minggu
                if ($date->isWeekend()) {
                    continue;
                }

                // Random Status (80% Hadir, sisanya aneh-aneh)
                $rand = rand(1, 100);

                if ($rand <= 80) {
                    $status = 'Hadir';
                } elseif ($rand <= 85) {
                    $status = 'Telat';
                } elseif ($rand <= 90) {
                    $status = 'Sakit';
                } elseif ($rand <= 95) {
                    $status = 'Ijin';
                } else {
                    $status = 'Tanpa Keterangan'; // Alpha
                }

                // Setup Data per Status
                $checkIn = null;
                $checkOut = null;
                $location = null;
                $locationName = '-';

                if ($status === 'Hadir') {
                    $checkIn = '07:' . str_pad(rand(15, 59), 2, '0', STR_PAD_LEFT) . ':00';
                    $checkOut = '16:' . str_pad(rand(0, 30), 2, '0', STR_PAD_LEFT) . ':00';
                    $location = '-7.5949176,110.9406551'; // Koordinat Bapperida
                    $locationName = 'KANTOR BAPPERIDA';
                }
                elseif ($status === 'Telat') {
                    $checkIn = '08:' . str_pad(rand(10, 45), 2, '0', STR_PAD_LEFT) . ':00'; // Datang siang
                    $checkOut = '16:00:00';
                    $location = '-7.5949176,110.9406551';
                    $locationName = 'KANTOR BAPPERIDA';
                }
                elseif ($status === 'Sakit') {
                    $locationName = 'RUMAH / RS';
                }
                elseif ($status === 'Ijin') {
                    $locationName = 'KEPERLUAN KELUARGA';
                }
                elseif ($status === 'Dinas Luar') {
                    $locationName = 'DINAS LUAR (PEMKAB)';
                }

                Attendance::create([
                    'user_id' => $student->id,
                    'tanggal' => $date->format('Y-m-d'),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => $status,
                    'location' => $location,
                    'location_name' => $locationName,
                ]);
            }
        }
    }
}
