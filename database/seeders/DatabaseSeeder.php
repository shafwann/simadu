<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Password Universal (Biar login gampang)
        $password = Hash::make('12345678');

        // Gunakan Faker Bahasa Indonesia
        $faker = \Faker\Factory::create('id_ID');

        // ==========================================
        // DATA RANDOM PENDUKUNG
        // ==========================================

        // Daftar Bidang Magang (Tanpa Administrator Sistem)
        $pilihanBidangMahasiswa = [
            'Ekonomi',
            'Sekretariat',
            'Sosial & Budaya',
            'Fisik & Prasarana',
            'Penelitian, Pengembangan & Penyusunan Program'
        ];

        // Daftar Kampus (Agar terlihat nyata)
        $daftarKampus = [
            'Universitas Sebelas Maret (UNS)',
            'Universitas Muhammadiyah Surakarta (UMS)',
            'Universitas Diponegoro (UNDIP)',
            'Universitas Dian Nuswantoro (UDINUS)',
            'Universitas Gadjah Mada (UGM)',
            'Universitas Negeri Semarang (UNNES)',
            'Politeknik Negeri Semarang',
            'Universitas Amikom Yogyakarta'
        ];

        // Daftar Prodi
        $daftarProdi = [
            'Teknik Informatika',
            'Sistem Informasi',
            'Manajemen',
            'Akuntansi',
            'Ilmu Komunikasi',
            'Hukum',
            'Administrasi Publik'
        ];

        // ==========================================
        // 1. BUAT AKUN SUPER ADMIN (ADMINISTRATOR SISTEM)
        // ==========================================
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@bapperlitbang.com',
            'password' => $password,
            'role' => 'admin',
            'bidang' => 'Administrator Sistem',
            'status' => 'aktif',
            // Admin tidak perlu NIM/Instansi (Null)
        ]);

        $this->command->info('✅ Akun Super Admin berhasil dibuat.');

        // ==========================================
        // 2. BUAT 15 AKUN MAHASISWA RANDOM
        // ==========================================
        for ($i = 1; $i <= 100; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->userName . '@student.com',
                'password' => $password,
                'role' => 'mahasiswa',
                'bidang' => $faker->randomElement($pilihanBidangMahasiswa), // Pilih Bidang Acak
                'status' => 'aktif',

                // --- DATA RANDOM BARU ---
                // Membuat 8 digit angka unik untuk NIM (cth: 21010344)
                'nim' => $faker->unique()->numerify('########'),

                // Pilih Kampus & Prodi Acak
                'instansi' => $faker->randomElement($daftarKampus),
                'prodi' => $faker->randomElement($daftarProdi),
            ]);
        }

        $this->command->info('✅ 100 Akun Mahasiswa dengan NIM & Instansi berhasil dibuat.');

        // ==========================================
        // 3. PANGGIL SEEDER ADMIN PER BIDANG
        // ==========================================
        $this->call([
            AdminBidangSeeder::class,
        ]);
    }
}
