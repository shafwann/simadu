<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AboutSetting; // Jangan lupa import ini
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Password Universal
        $password = Hash::make('12345678');
        $faker = \Faker\Factory::create('id_ID');

        // ==========================================
        // DATA RANDOM PENDUKUNG
        // ==========================================

        // Daftar Kategori Instansi (Multi-Tenancy)
        $daftarInstansiPemerintah = [
            'BAPPERIDA', // Default
            'DISKOMINFO',
            'DISDIKBUD',
            'DINKES',
            'DPUPR',
            'DISPARPORA'
        ];

        $pilihanBidangMahasiswa = [
            'Ekonomi',
            'Sekretariat',
            'Sosial & Budaya',
            'Fisik & Prasarana',
            'Penelitian, Pengembangan & Penyusunan Program',
            'IT & Jaringan', // Tambahan buat anak TI
            'Humas & Publikasi'
        ];

        $daftarKampus = [
            'Universitas Sebelas Maret (UNS)',
            'Universitas Muhammadiyah Surakarta (UMS)',
            'Universitas Diponegoro (UNDIP)',
            'Universitas Dian Nuswantoro (UDINUS)',
            'Universitas Gadjah Mada (UGM)',
            'Universitas Negeri Semarang (UNNES)',
            'Politeknik Negeri Semarang',
            'Universitas Amikom Yogyakarta',
            'SMK Negeri 1 Karanganyar',
            'SMK Negeri 2 Surakarta',
            'Universitas Veteran Yogyakarta(UPN)',
            'Institut Teknologi Sepuluh Nopember (ITS)',
            'Institut Pertanian Bogor (IPB)',
            'Universitas Indonesia (UI)',
            'Universitas Padjadjaran (UNPAD)'
        ];

        $daftarProdi = [
            'Teknik Informatika', 'Sistem Informasi', 'Manajemen',
            'Akuntansi', 'Ilmu Komunikasi', 'Hukum', 'Administrasi Publik',
            'Teknik Sipil', 'Kesehatan Masyarakat', 'PGSD'
        ];

        // ==========================================
        // 1. BUAT SUPER ADMIN & SETTING UNTUK SETIAP INSTANSI
        // ==========================================

        foreach ($daftarInstansiPemerintah as $instansi) {

            // Skip BAPPERIDA disini karena sudah dihandle AdminBidangSeeder (biar gak double)
            if ($instansi === 'BAPPERIDA') continue;

            // A. Buat User Super Admin per Instansi
            User::create([
                'name' => 'Super Admin ' . $instansi,
                'email' => 'admin.' . strtolower($instansi) . '@karanganyar.go.id',
                'password' => $password,
                'role' => 'admin',
                'bidang' => 'Administrator Sistem',
                'status' => 'aktif',
                'kategori_instansi' => $instansi, // ✅ PENTING: Penanda Lobby
            ]);

            // B. Buat Default AboutSetting (Supaya dashboard tidak error)
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
        }

        $this->command->info('✅ Super Admin & Settings untuk semua instansi berhasil dibuat.');


        // ==========================================
        // 2. BUAT 100 AKUN MAHASISWA RANDOM (TERSEBAR)
        // ==========================================

        for ($i = 1; $i <= 100; $i++) {

            $tglMasuk = $faker->dateTimeBetween('-3 months', 'now');
            $tglSelesai = (clone $tglMasuk)->modify('+3 months');

            // Pilih Instansi Tujuan secara Acak
            $instansiTujuan = $faker->randomElement($daftarInstansiPemerintah);

            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->userName . '@student.com',
                'password' => $password,
                'role' => 'mahasiswa',
                'status' => 'aktif',

                // ✅ KUNCI: Mahasiswa ini akan masuk ke lobby instansi mana?
                'kategori_instansi' => $instansiTujuan,

                // Data Akademik
                'bidang' => $faker->randomElement($pilihanBidangMahasiswa),
                'nim' => $faker->unique()->numerify('########'),
                'instansi' => $faker->randomElement($daftarKampus), // Asal Kampus
                'prodi' => $faker->randomElement($daftarProdi),
                'fakultas' => $faker->randomElement(['Teknik', 'Ekonomi', 'Sosial', null]),
                'kegiatan' => $faker->randomElement(['Magang Reguler', 'PKL SMK', 'MBKM']),
                'tanggal_masuk' => $tglMasuk,
                'tanggal_selesai' => $tglSelesai,
            ]);
        }

        $this->command->info('✅ 100 Mahasiswa berhasil disebar ke berbagai instansi.');

        // ==========================================
        // 3. PANGGIL SEEDER UTAMA LAINNYA
        // ==========================================
        $this->call([
            MasterAdminSeeder::class, // Akun Master
            AdminBidangSeeder::class, // Akun Admin Bapperida (Divisi)
            AboutSettingSeeder::class, // Setting khusus Bapperida
            AttendanceSeeder::class, // Data Kehadiran awal
            LogbookSeeder::class,    // Data Logbook awal
        ]);
    }
}
