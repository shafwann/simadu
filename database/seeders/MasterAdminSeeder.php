<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MasterAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Master Super Admin',
            'email' => 'master@system.com',
            'password' => Hash::make('12345678'), // Ganti password yang kuat
            'role' => 'master',
            'kategori_instansi' => null, // Master tidak punya kategori
            'bidang' => 'System Owner',
            'status' => 'aktif',
        ]);
    }
}
