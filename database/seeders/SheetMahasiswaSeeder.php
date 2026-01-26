<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SheetMahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $rawStudents = [
            ['Farida Putri Anggraini', '22210031', 'Magang Kerja', 'Akuntansi', 'Universitas Slamet Riyadi', '2025-09-03', '2026-01-03'],
            ['Amelia Dwi Rahmawati', '22210016', 'Magang Kerja', 'Akuntansi', 'Universitas Slamet Riyadi', '2025-09-03', '2026-01-03'],
            ['Lutfiah Umi Maslamah', '22210017', 'Magang Kerja', 'Akuntansi', 'Universitas Slamet Riyadi', '2025-09-03', '2026-01-03'],
            ['Laila Zahroh Tuh Sitah', '12500', 'Praktik Kerja Lapangan', 'Teknik Jaringan Komputer', 'SMK Muhammadiyah 2', '2025-08-04', '2026-01-05'],
            ['Rinanda Dwi Pratiwi', '12514', 'Praktik Kerja Lapangan', 'Teknik Jaringan Komputer', 'SMK Muhammadiyah 2', '2025-08-04', '2026-01-05'],
            ['Kessya Attha Amadea Mosza', '12496', 'Praktik Kerja Lapangan', 'Teknik Jaringan Komputer', 'SMK Muhammadiyah 2', '2025-08-04', '2026-01-05'],
            ['Virda Stefany Amelia Manik', '123220132', 'Kerja Praktik', 'Informatika', 'Universitas Pembangunan Nasional “Veteran”', '2026-01-01', '2026-01-31'],
            ['Aisyah Putri Amanah', 'M0123003', 'Magang', 'S-1 Matematika', 'Universitas Sebelas Maret', '2026-01-05', '2026-02-06'],
            ['Alya Regina Putri Wijaya', 'M0123006', 'Magang', 'S-1 Matematika', 'Universitas Sebelas Maret', '2026-01-05', '2026-02-06'],
            ['Annisa Hidayat Nur Fadhillah', 'M0123010', 'Magang', 'S-1 Matematika', 'Universitas Sebelas Maret', '2026-01-05', '2026-02-06'],
            ['Arzety Seftiya Putri', 'M0123014', 'Magang', 'S-1 Matematika', 'Universitas Sebelas Maret', '2026-01-05', '2026-02-06'],
            ['Dian Ayu Shintawati', 'M0123025', 'Magang', 'S-1 Matematika', 'Universitas Sebelas Maret', '2026-01-05', '2026-02-06'],
            ['Dufa Alwan Farras', 'M0123026', 'Magang', 'S-1 Matematika', 'Universitas Sebelas Maret', '2026-01-05', '2026-02-06'],
            ['Daffa Ikhwanul Aulia', 'M0123021', 'Magang', 'S-1 Matematika', 'Universitas Sebelas Maret', '2026-01-05', '2026-02-06'],
            ['Muhammad Ilham Priambudi', 'M0123060', 'Magang', 'S-1 Matematika', 'Universitas Sebelas Maret', '2026-01-05', '2026-02-06'],
            ['Muhammad Aspa Zikra', 'L0122105', 'Magang', 'Informatika', 'Universitas Sebelas Maret', '2025-12-22', '2026-02-06'],
            ['Aji Prasetyo', 'K5422003', 'Kuliah Kerja Magang', 'S-1 Pendidikan Geografi', 'Universitas Sebelas Maret', '2025-12-22', '2026-02-27'],
            ['Riskianawati', 'K5422075', 'Kuliah Kerja Magang', 'S-1 Pendidikan Geografi', 'Universitas Sebelas Maret', '2025-12-22', '2026-02-27'],
            ['Alya Imroatul Azizah', 'M0723006', 'Magang', 'S-1 Statistika', 'Universitas Sebelas Maret', '2026-01-05', '2026-02-06'],
            ['Meitha Qonita Aulia', 'M0723052', 'Magang', 'S-1 Statistika', 'Universitas Sebelas Maret', '2026-01-05', '2026-02-06'],
            ['Dzaky Mutaqoful Fikri', 'F0123043', 'Magang', 'Ekonomi Pembangunan', 'Universitas Sebelas Maret', '2025-12-29', '2026-02-13'],
            ["Maghfira I'zzani R. M.", 'F0123073', 'Magang', 'Ekonomi Pembangunan', 'Universitas Sebelas Maret', '2025-12-29', '2026-02-13'],
            ['Sandy Aziz Nur Hidayat', 'B0323065', 'Kuliah Kerja Magang', 'Sastra Inggris', 'Universitas Sebelas Maret', '2026-01-05', '2026-02-15'],
            ['Aura Sherylia Putri Fadilla', '124230008', 'Praktik Kerja', 'Sistem Informasi Program', 'Universitas Pembangunan Nasional “Veteran”', '2026-01-05', '2026-02-20'],
            ['Adnan Dio Ardana', '124230152', 'Praktik Kerja', 'Sistem Informasi Program', 'Universitas Pembangunan Nasional “Veteran”', '2026-01-05', '2026-02-20'],
            ['Ervia Rizqi Rahmawati', '1103624038', 'Magang Mandiri', 'Manajemen Pendidikan', 'Universitas Negeri Jakarta', '2026-01-19', '2026-02-20'],
            ['Ridha Ainaya Qalbi Annura', 'L0123121', 'Kegiatan Magang Mahasiswa', 'Informatika', 'Universitas Sebelas Maret', '2026-01-05', '2026-02-27'],
            ['Shofi Zahrotul Aulia', 'L0123131', 'Kegiatan Magang Mahasiswa', 'Informatika', 'Universitas Sebelas Maret', '2026-01-05', '2026-02-27'],
            ['Irene Oktavia Wananditasari', 'K5423042', 'Kuliah Kerja Magang', 'S-1 Pendidikan Geografi', 'Universitas Sebelas Maret', '2026-01-05', '2026-02-27'],
            ['Andini Hassya Artsitania', 'K5423004', 'Kuliah Kerja Magang', 'S-1 Pendidikan Geografi', 'Universitas Sebelas Maret', '2026-01-05', '2026-02-27'],
            ['Dhian Novyta Azharie', 'K5423021', 'Kuliah Kerja Magang', 'S-1 Pendidikan Geografi', 'Universitas Sebelas Maret', '2026-01-05', '2026-02-27'],
            ['Feby Bunga Arum Handayani', '16813', 'Praktik Kerja Lapangan', 'Manajemen Perkantoran', 'SMK Negeri 1 Karanganyar', '2025-11-03', '2026-04-05'],
            ['Hanisa Dyah Nur Aini', '23/515684/SV/22601', 'Magang MBKM', 'Pembangunan Ekonomi Kewilayahan', 'Universitas Gadjah Mada', '2026-01-19', '2026-02-20'],
            ['Mayva Wulandari', '23/522576/SV/23726', 'Magang MBKM', 'Pembangunan Ekonomi Kewilayahan', 'Universitas Gadjah Mada', '2026-01-19', '2026-02-20'],
            ['Lufvia Titana Lestari', '23/515116/SV/22482', 'Magang MBKM', 'Pembangunan Ekonomi Kewilayahan', 'Universitas Gadjah Mada', '2026-01-19', '2026-05-29'],
        ];

        foreach ($rawStudents as $student) {
            $nama     = $student[0];
            $nim      = $student[1];
            $kegiatan = $student[2];
            $prodi    = $student[3];
            $instansi = $student[4];
            $masuk    = $student[5];
            $selesai  = $student[6];

            // Cek Duplikasi NIM (Agar tidak error jika dijalankan ulang)
            if (User::where('nim', $nim)->exists()) {
                continue;
            }

            // Cek Duplikasi Email (Ini yang bikin error sebelumnya)
            // Kita buat emailnya PASTI unik dengan memasukkan NIM lengkap
            // Hapus karakter aneh dari NIM (seperti /)
            $cleanNim = preg_replace('/[^a-zA-Z0-9]/', '', $nim);
            $cleanName = strtolower(explode(' ', $nama)[0]);
            $email = $cleanName . '.' . $cleanNim . '@student.com';

            // Logic Status Otomatis
            $isFinished = Carbon::parse($selesai)->lt(Carbon::now());
            $statusAkun = $isFinished ? 'nonaktif' : 'aktif';

            User::create([
                'nim'               => $nim,
                'name'              => $nama,
                'email'             => $email, // <--- EMAIL SUDAH UNIK SEKARANG
                'password'          => Hash::make('password'),
                'role'              => 'mahasiswa',
                'status'            => $statusAkun,

                // Detail Magang
                'kegiatan'          => $kegiatan,
                'instansi'          => $instansi,
                'prodi'             => $prodi,

                // REQUEST: Penempatan BAPPERIDA & Pembimbing NULL
                'kategori_instansi' => 'BAPPERIDA',
                'bidang'            => null,
                'nama_pembimbing'    => null,
                'jabatan_pembimbing' => null,
                'nip_pembimbing'     => null,

                // Tanggal
                'tanggal_masuk'     => $masuk,
                'tanggal_selesai'   => $selesai,
            ]);
        }
    }
}
