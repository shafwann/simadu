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
            $table->string('social_youtube')->nullable();
            $table->string('social_x')->nullable();
            $table->longText('custom_links')->nullable(); // Menyimpan JSON Link Terkait
        });
    }

    public function down(): void
    {
        Schema::table('about_settings', function (Blueprint $table) {
            $table->dropColumn(['social_youtube', 'social_x', 'custom_links']);
        });
    }
};
