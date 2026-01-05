<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminBidangSeeder extends Seeder
{
    public function run(): void
    {
        $bidangs = [
            'Administrator Sistem', // Nama baru
            'Ekonomi',
            'Sekretariat',
            'Sosial & Budaya',
            'Fisik & Prasarana',
            'Penelitian, Pengembangan & Penyusunan Program'
        ];

        foreach ($bidangs as $bidang) {
            $slug = Str::slug($bidang);

            // Logic Email:
            // 1. Jika Administrator Sistem -> superadmin@bapperlitbang.com
            // 2. Jika Lainnya -> admin.namabidang@bapperlitbang.com

            if ($bidang === 'Administrator Sistem') {
                $email = 'superadmin@bapperlitbang.com';
                $name = 'Super Admin BAPPERIDA';
            } else {
                $email = "admin.{$slug}@bapperlitbang.com";
                $name = 'Admin Bidang ' . $bidang;
            }

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('12345678'),
                    'role' => 'admin',     // Role tetap admin (hak akses penuh)
                    'bidang' => $bidang,   // Bidang sesuai divisi
                    'email_verified_at' => now(),
                    'status' => 'aktif',   // Pastikan status aktif
                ]
            );
        }
    }
}
