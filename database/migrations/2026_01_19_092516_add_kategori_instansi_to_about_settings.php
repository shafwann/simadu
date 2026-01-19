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
            // Simpan nama instansi pemilik setting ini (misal: "DISKOMINFO")
            $table->string('kategori_instansi')->nullable()->index()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('about_settings', function (Blueprint $table) {
            $table->dropColumn('kategori_instansi');
        });
    }
};
