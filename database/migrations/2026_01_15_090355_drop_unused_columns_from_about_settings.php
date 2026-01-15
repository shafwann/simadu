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
            // Hapus kolom whatsapp dan link statis karena sudah diganti fitur lain
            $table->dropColumn(['whatsapp_number', 'link_website', 'link_ppid', 'link_opendata']);
        });
    }

    public function down(): void
    {
        // Jika rollback, buat lagi (nullable)
        Schema::table('about_settings', function (Blueprint $table) {
            $table->string('whatsapp_number')->nullable();
            $table->string('link_website')->nullable();
            $table->string('link_ppid')->nullable();
            $table->string('link_opendata')->nullable();
        });
    }
};
