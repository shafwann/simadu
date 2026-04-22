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
        if (! Schema::hasTable('about_settings') || Schema::hasColumn('about_settings', 'dokumen_terkait')) {
            return;
        }

        Schema::table('about_settings', function (Blueprint $table) {
            $table->longText('dokumen_terkait')->nullable()->after('custom_links');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('about_settings') || ! Schema::hasColumn('about_settings', 'dokumen_terkait')) {
            return;
        }

        Schema::table('about_settings', function (Blueprint $table) {
            $table->dropColumn('dokumen_terkait');
        });
    }
};
