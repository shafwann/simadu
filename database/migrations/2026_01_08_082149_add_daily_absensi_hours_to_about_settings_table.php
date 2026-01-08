<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('about_settings', function (Blueprint $table) {
            // Senin
            $table->time('senin_mulai')->nullable()->after('jam_operasional');
            $table->time('senin_selesai')->nullable()->after('senin_mulai');

            // Selasa
            $table->time('selasa_mulai')->nullable()->after('senin_selesai');
            $table->time('selasa_selesai')->nullable()->after('selasa_mulai');

            // Rabu
            $table->time('rabu_mulai')->nullable()->after('selasa_selesai');
            $table->time('rabu_selesai')->nullable()->after('rabu_mulai');

            // Kamis
            $table->time('kamis_mulai')->nullable()->after('rabu_selesai');
            $table->time('kamis_selesai')->nullable()->after('kamis_mulai');

            // Jumat
            $table->time('jumat_mulai')->nullable()->after('kamis_selesai');
            $table->time('jumat_selesai')->nullable()->after('jumat_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('about_settings', function (Blueprint $table) {
            $table->dropColumn([
                'senin_mulai','senin_selesai',
                'selasa_mulai','selasa_selesai',
                'rabu_mulai','rabu_selesai',
                'kamis_mulai','kamis_selesai',
                'jumat_mulai','jumat_selesai',
            ]);
        });
    }
};
