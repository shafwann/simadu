<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('about_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_instansi')->nullable();
            $table->string('kepala_instansi')->nullable();
            $table->string('nip_kepala_instansi')->nullable();
            $table->string('jam_operasional')->nullable();

            // LOKASI
            $table->decimal('office_latitude', 10, 7)->nullable();
            $table->decimal('office_longitude', 10, 7)->nullable();
            $table->integer('radius_meter')->default(50);

            // CHECK-IN
            $table->time('senin_mulai')->nullable();
            $table->time('senin_selesai')->nullable();
            $table->time('selasa_mulai')->nullable();
            $table->time('selasa_selesai')->nullable();
            $table->time('rabu_mulai')->nullable();
            $table->time('rabu_selesai')->nullable();
            $table->time('kamis_mulai')->nullable();
            $table->time('kamis_selesai')->nullable();
            $table->time('jumat_mulai')->nullable();
            $table->time('jumat_selesai')->nullable();

            // CHECK-OUT
            $table->time('senin_checkout_mulai')->nullable();
            $table->time('senin_checkout_selesai')->nullable();
            $table->time('selasa_checkout_mulai')->nullable();
            $table->time('selasa_checkout_selesai')->nullable();
            $table->time('rabu_checkout_mulai')->nullable();
            $table->time('rabu_checkout_selesai')->nullable();
            $table->time('kamis_checkout_mulai')->nullable();
            $table->time('kamis_checkout_selesai')->nullable();
            $table->time('jumat_checkout_mulai')->nullable();
            $table->time('jumat_checkout_selesai')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_settings');
    }
};
