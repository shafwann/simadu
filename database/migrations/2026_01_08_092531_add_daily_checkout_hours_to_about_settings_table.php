<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('about_settings', function (Blueprint $table) {
            // SENIN
            $table->time('senin_checkout_mulai')->nullable()->after('jumat_selesai');
            $table->time('senin_checkout_selesai')->nullable()->after('senin_checkout_mulai');

            // SELASA
            $table->time('selasa_checkout_mulai')->nullable()->after('senin_checkout_selesai');
            $table->time('selasa_checkout_selesai')->nullable()->after('selasa_checkout_mulai');

            // RABU
            $table->time('rabu_checkout_mulai')->nullable()->after('selasa_checkout_selesai');
            $table->time('rabu_checkout_selesai')->nullable()->after('rabu_checkout_mulai');

            // KAMIS
            $table->time('kamis_checkout_mulai')->nullable()->after('rabu_checkout_selesai');
            $table->time('kamis_checkout_selesai')->nullable()->after('kamis_checkout_mulai');

            // JUMAT
            $table->time('jumat_checkout_mulai')->nullable()->after('kamis_checkout_selesai');
            $table->time('jumat_checkout_selesai')->nullable()->after('jumat_checkout_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('about_settings', function (Blueprint $table) {
            $table->dropColumn([
                'senin_checkout_mulai','senin_checkout_selesai',
                'selasa_checkout_mulai','selasa_checkout_selesai',
                'rabu_checkout_mulai','rabu_checkout_selesai',
                'kamis_checkout_mulai','kamis_checkout_selesai',
                'jumat_checkout_mulai','jumat_checkout_selesai',
            ]);
        });
    }
};
