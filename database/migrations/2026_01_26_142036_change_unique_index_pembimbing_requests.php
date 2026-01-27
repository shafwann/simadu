<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembimbing_requests', function (Blueprint $table) {
            // Hapus unique index lama (global)
            // Nama index biasanya: pembimbing_requests_nidn_unique & pembimbing_requests_email_unique
            // (sesuai error kamu, nidn index name benar)
            $table->dropUnique('pembimbing_requests_nidn_unique');
            $table->dropUnique('pembimbing_requests_email_unique');

            // Buat unique gabungan per mahasiswa
            $table->unique(['mahasiswa_id', 'nidn'], 'pr_mahasiswa_nidn_unique');
            $table->unique(['mahasiswa_id', 'email'], 'pr_mahasiswa_email_unique');
        });
    }

    public function down(): void
    {
        Schema::table('pembimbing_requests', function (Blueprint $table) {
            // Balikin seperti semula
            $table->dropUnique('pr_mahasiswa_nidn_unique');
            $table->dropUnique('pr_mahasiswa_email_unique');

            $table->unique('nidn');
            $table->unique('email');
        });
    }
};