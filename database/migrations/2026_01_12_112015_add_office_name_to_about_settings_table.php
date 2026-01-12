<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('about_settings', function (Blueprint $table) {
            // Tambahkan kolom office_name setelah office_longitude
            $table->string('office_name')->nullable()->after('office_longitude');
        });
    }

    public function down(): void
    {
        Schema::table('about_settings', function (Blueprint $table) {
            $table->dropColumn('office_name');
        });
    }
};
