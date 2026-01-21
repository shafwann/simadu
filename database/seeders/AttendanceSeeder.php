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
        // 1. DAFTAR KOORDINAT KANTOR (Pusat di Karanganyar)
        $kantorCoordinates = [
            'BAPPERIDA' => [
                'lat' => -7.5949176,
                'lng' => 110.9406551,
                'name' => 'KANTOR BAPPERIDA'
            ],
            'DISKOMINFO' => [
                'lat' => -7.5972000,
                'lng' => 110.9421000,
                'name' => 'KANTOR DISKOMINFO'
            ],
            'DISDIKBUD' => [
                'lat' => -7.5935000,
                'lng' => 110.9392000,
                'name' => 'KANTOR DISDIKBUD'
            ],
            'DINKES' => [
                'lat' => -7.5928000,
                'lng' => 110.9385000,
                'name' => 'KANTOR DINAS KESEHATAN'
            ],
            'DPUPR' => [
                'lat' => -7.5991000,
                'lng' => 110.9453000,
                'name' => 'KANTOR DPUPR'
            ],
            'DISPARPORA' => [
                'lat' => -7.5960000,
                'lng' => 110.9415000,
                'name' => 'KANTOR DISPARPORA'
            ],
            // Default Fallback
            'UMUM' => [
                'lat' => -7.5949176,
                'lng' => 110.9406551,
                'name' => 'KANTOR PEMDA'
            ]
        ];

        // Ambil semua mahasiswa
        $students = User::where('role', 'mahasiswa')->get();

        foreach ($students as $student) {

            // Tentukan Kantor Berdasarkan Kategori Instansi Mahasiswa
            $kategori = $student->kategori_instansi ?? 'BAPPERIDA'; // Default ke Bapperida

            // Ambil data koordinat (Fallback ke Bapperida jika kategori tidak ada di list)
            $officeData = $kantorCoordinates[$kategori] ?? $kantorCoordinates['BAPPERIDA'];

            // Kita buatkan data untuk 15 hari kerja pertama sejak tanggal masuk
            // Jika tanggal masuk null, pakai tanggal hari ini mundur 15 hari
            $startDate = $student->tanggal_masuk ? Carbon::parse($student->tanggal_masuk) : Carbon::now()->subDays(20);

            for ($i = 0; $i < 15; $i++) {
                $date = $startDate->copy()->addDays($i);

                // Skip Sabtu & Minggu
                if ($date->isWeekend()) {
                    continue;
                }

                // Random Status (80% Hadir)
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

                    // Generate Random Jitter (Agar titik GPS tidak menumpuk 100% sama)
                    // Variasi sekitar 5-10 meter
                    $lat = $officeData['lat'] + (rand(-50, 50) / 1000000);
                    $lng = $officeData['lng'] + (rand(-50, 50) / 1000000);

                    $location = "{$lat},{$lng}";
                    $locationName = $officeData['name'];
                }
                elseif ($status === 'Telat') {
                    $checkIn = '08:' . str_pad(rand(10, 45), 2, '0', STR_PAD_LEFT) . ':00'; // Datang siang
                    $checkOut = '16:00:00';

                    $lat = $officeData['lat'] + (rand(-50, 50) / 1000000);
                    $lng = $officeData['lng'] + (rand(-50, 50) / 1000000);

                    $location = "{$lat},{$lng}";
                    $locationName = $officeData['name'];
                }
                elseif ($status === 'Sakit') {
                    $locationName = 'RUMAH / RS';
                }
                elseif ($status === 'Ijin') {
                    $locationName = 'KEPERLUAN KELUARGA';
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
