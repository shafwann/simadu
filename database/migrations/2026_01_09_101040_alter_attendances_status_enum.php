<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', [
                'Hadir',
                'Telat',
                'Ijin',
                'Sakit',
                'Dinas Luar',
                'Cuti',
                'Tanpa Keterangan'
            ])->default('Hadir')->change();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', [
                'hadir',
                'izin',
                'alpha',
                'sakit',
                'telat'
            ])->default('hadir')->change();
        });
    }
};
