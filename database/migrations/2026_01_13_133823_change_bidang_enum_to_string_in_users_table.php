<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ubah enum -> string
            $table->string('bidang')->default('Administrator Sistem')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // rollback ke enum (optional, jarang dipakai)
            $table->enum('bidang', [
                'Administrator Sistem',
                'Ekonomi',
                'Sekretariat',
                'Sosial & Budaya',
                'Fisik & Prasarana',
                'Penelitian, Pengembangan & Penyusunan Program'
            ])->default('Administrator Sistem')->change();
        });
    }
};
