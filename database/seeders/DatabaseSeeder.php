<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Password Universal
        $password = Hash::make('12345678');

        // Gunakan Faker Bahasa Indonesia
        $faker = \Faker\Factory::create('id_ID');

        // ... (Kode array data pendukung tetap sama) ...
        $pilihanBidangMahasiswa = [
            'Ekonomi', 'Sekretariat', 'Sosial & Budaya',
            'Fisik & Prasarana', 'Penelitian, Pengembangan & Penyusunan Program'
        ];

        $daftarKampus = [
            'Universitas Sebelas Maret (UNS)', 'Universitas Muhammadiyah Surakarta (UMS)',
            'Universitas Diponegoro (UNDIP)', 'Universitas Dian Nuswantoro (UDINUS)',
            'Universitas Gadjah Mada (UGM)', 'Universitas Negeri Semarang (UNNES)',
            'Politeknik Negeri Semarang', 'Universitas Amikom Yogyakarta',
            'SMK Negeri 1 Karanganyar', 'SMK Negeri 2 Surakarta',
            'Universitas Veteran Yogyakarta(UPN)', 'Institut Teknologi Sepuluh Nopember (ITS)',
            'Institut Pertanian Bogor (IPB)', 'Universitas Indonesia (UI)',
            'Universitas Padjadjaran (UNPAD)'
        ];

        $daftarProdi = [
            'Teknik Informatika', 'Sistem Informasi', 'Manajemen', 'Akuntansi',
            'Ilmu Komunikasi', 'Hukum', 'Administrasi Publik'
        ];

        // 1. BUAT AKUN SUPER ADMIN
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@bapperlitbang.com',
            'password' => $password,
            'role' => 'admin',
            'bidang' => 'Administrator Sistem',
            'status' => 'aktif',
        ]);

        $this->command->info('✅ Akun Super Admin berhasil dibuat.');

        // 2. BUAT 20 AKUN MAHASISWA RANDOM
        $tglMasuk = $faker->dateTimeBetween('-3 months', 'now');
        $tglSelesai = (clone $tglMasuk)->modify('+3 months');

        for ($i = 1; $i <= 20; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->userName . '@student.com',
                'password' => $password,
                'role' => 'mahasiswa',
                'bidang' => $faker->randomElement($pilihanBidangMahasiswa),
                'status' => 'aktif',
                'nim' => $faker->unique()->numerify('########'),
                'instansi' => $faker->randomElement($daftarKampus),
                'prodi' => $faker->randomElement($daftarProdi),
                'fakultas' => $faker->randomElement(['Teknik', 'Ekonomi & Bisnis', 'Ilmu Sosial', null]),
                'kegiatan' => $faker->randomElement(['Magang Reguler', 'PKL SMK', 'Penelitian Skripsi', 'MBKM','Kuliah Kerja Nyata']),
                'tanggal_masuk' => $tglMasuk,
                'tanggal_selesai' => $tglSelesai,
            ]);
        }

        $this->command->info('✅ 20 Akun Mahasiswa berhasil dibuat.');

        // ==========================================
        // 3. PANGGIL SEMUA SEEDER DI SINI (CUKUP SEKALI)
        // ==========================================
        $this->call([
            AdminBidangSeeder::class,   // Membuat admin per bidang
            AboutSettingSeeder::class,  // Setting aplikasi
            AttendanceSeeder::class,    // Presensi dummy
            LogbookSeeder::class,       // Logbook dummy
        ]);

        $this->command->info('✅ Data Admin, Setting, Presensi & Logbook berhasil di-generate.');
    }
}
