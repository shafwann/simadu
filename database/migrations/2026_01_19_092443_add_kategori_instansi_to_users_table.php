<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom teks biasa, tidak perlu relasi foreign key
            // Nullable untuk Master Admin yang tidak terikat instansi
            $table->string('kategori_instansi')->nullable()->index()->after('role');

            // Update enum role jika belum ada 'master' (opsional jika sudah ada)
            // DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('master', 'admin', 'mahasiswa', 'dosen') ...");
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('kategori_instansi');
        });
    }
};
