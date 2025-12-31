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
        // 1. Password Universal (dibuat sekali biar cepat)
        $password = Hash::make('12345678');

        // Gunakan Faker Bahasa Indonesia biar namanya lokal
        $faker = \Faker\Factory::create('id_ID');

        // ==========================================
        // BUAT 1 AKUN ADMIN
        // ==========================================
        User::create([
            'name' => 'Admin Baperlitbang',
            'email' => 'admin@baperlitbang.com',
            'password' => $password,
            'role' => 'admin',
        ]);

        $this->command->info('✅ Akun Admin berhasil dibuat: admin@baperlitbang.com');

        // ==========================================
        // BUAT 10 AKUN MAHASISWA RANDOM
        // ==========================================
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->userName . '@student.com', // email random
                'password' => $password,
                'role' => 'mahasiswa',
            ]);
        }

        $this->command->info('✅ 10 Akun Mahasiswa random berhasil dibuat.');
    }
}
