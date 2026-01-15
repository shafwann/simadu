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
        Schema::table('about_settings', function (Blueprint $table) {
            // Kontak & Alamat
            $table->text('office_address')->nullable(); // Alamat Lengkap
            $table->string('office_phone')->nullable(); // No Telp (0271...)
            $table->string('office_email')->nullable(); // Email (opsional)

            // Sosmed (Simpan URL lengkap)
            $table->string('social_instagram')->nullable();
            $table->string('social_facebook')->nullable();

            // Google Maps Embed (URL src dari iframe)
            $table->text('google_maps_embed')->nullable();

            // WhatsApp (Sapa Mas)
            $table->string('whatsapp_number')->nullable(); // Format 628...

            // Link Terkait (URL)
            $table->string('link_website')->nullable(); // Web Utama
            $table->string('link_ppid')->nullable();    // PPID
            $table->string('link_opendata')->nullable(); // Open Data
        });
    }

    public function down(): void
    {
        Schema::table('about_settings', function (Blueprint $table) {
            $table->dropColumn([
                'office_address', 'office_phone', 'office_email',
                'social_instagram', 'social_facebook',
                'google_maps_embed', 'whatsapp_number',
                'link_website', 'link_ppid', 'link_opendata'
            ]);
        });
    }
};
