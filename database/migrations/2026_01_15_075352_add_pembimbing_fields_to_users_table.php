<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('jabatan_pembimbing')->nullable()->after('tanggal_selesai');
            $table->string('nama_pembimbing')->nullable()->after('jabatan_pembimbing');
            $table->string('nip_pembimbing')->nullable()->after('nama_pembimbing');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['jabatan_pembimbing', 'nama_pembimbing', 'nip_pembimbing']);
        });
    }
};

