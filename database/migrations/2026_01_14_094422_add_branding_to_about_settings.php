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
            $table->string('app_name')->nullable()->default('Bapperida'); // Judul Utama
            $table->string('agency_name')->nullable()->default('Kab. Karanganyar'); // Sub Judul
            $table->text('app_description')->nullable(); // Deskripsi Singkat
            $table->string('logo_path')->nullable(); // Path Gambar Logo
        });
    }

    public function down(): void
    {
        Schema::table('about_settings', function (Blueprint $table) {
            $table->dropColumn(['app_name', 'agency_name', 'app_description', 'logo_path']);
        });
    }
};
