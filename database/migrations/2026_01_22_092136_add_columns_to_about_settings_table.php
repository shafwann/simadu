<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('about_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('about_settings', 'kategori_instansi')) {
                $table->string('kategori_instansi')->default('UMUM')->index()->after('id');
            }

            foreach ([
                'app_name' => 'string',
                'agency_name' => 'string',
                'app_description' => 'string',
                'logo_path' => 'string',
                'office_name' => 'string',
            ] as $col => $type) {
                if (!Schema::hasColumn('about_settings', $col)) {
                    $table->{$type}($col)->nullable();
                }
            }

            if (!Schema::hasColumn('about_settings', 'bidang_options')) {
                $table->longText('bidang_options')->nullable();
            }

            if (!Schema::hasColumn('about_settings', 'custom_links')) {
                $table->longText('custom_links')->nullable();
            }

            // footer fields (kalau belum ada)
            foreach ([
                'office_address' => 'text',
                'office_phone' => 'string',
                'office_email' => 'string',
                'social_instagram' => 'string',
                'social_facebook' => 'string',
                'social_youtube' => 'string',
                'social_x' => 'string',
                'google_maps_embed' => 'longText',
                'whatsapp_number' => 'string',
            ] as $col => $type) {
                if (!Schema::hasColumn('about_settings', $col)) {
                    $table->{$type}($col)->nullable();
                }
            }

            // unique kategori_instansi (optional)
            // Kalau di table sudah ada duplikat kategori, jangan aktifkan sebelum beresin data.
            // $table->unique('kategori_instansi');
        });
    }

    public function down(): void
    {
        Schema::table('about_settings', function (Blueprint $table) {
            // rollback seperlunya
        });
    }
};
