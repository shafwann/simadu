<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('about_settings', function (Blueprint $table) {
            $table->id();

            // KEY UTAMA PER INSTANSI
            $table->string('kategori_instansi')->default('UMUM')->index();

            // BRANDING
            $table->string('app_name')->nullable()->default('Bapperida');
            $table->string('agency_name')->nullable()->default('Kab. Karanganyar');
            $table->text('app_description')->nullable();
            $table->string('logo_path')->nullable();

            // BIDANG OPTIONS (JSON)
            $table->longText('bidang_options')->nullable();

            // IDENTITAS INSTANSI
            $table->string('nama_instansi')->nullable();
            $table->string('kepala_instansi')->nullable();
            $table->string('nip_kepala_instansi')->nullable();
            $table->string('jam_operasional')->nullable();

            // LOKASI
            $table->string('office_name')->nullable();
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

            // FOOTER / SOSMED / MAP / WA / CUSTOM LINKS
            $table->text('office_address')->nullable();
            $table->string('office_phone')->nullable();
            $table->string('office_email')->nullable();
            $table->string('social_instagram')->nullable();
            $table->string('social_facebook')->nullable();
            $table->string('social_youtube')->nullable();
            $table->string('social_x')->nullable();
            $table->longText('google_maps_embed')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->longText('custom_links')->nullable();

            $table->timestamps();

            // 1 instansi = 1 record
            $table->unique('kategori_instansi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_settings');
    }
};
