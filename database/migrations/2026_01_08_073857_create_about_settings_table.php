<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('about_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_instansi')->nullable();
            $table->string('kepala_instansi')->nullable();
            $table->string('nip_kepala_instansi')->nullable();
            $table->string('jam_operasional')->nullable();
            $table->timestamps();
        });

        // INSERT DEFAULT DATA (HARUS DI DALAM up())
        DB::table('about_settings')->insert([
            'jam_operasional' => 'Senin 07:30-15:00, Selasa 07:30-15:00, Rabu 07:30-15:00, Kamis 07:30-15:00, Jumat 07:30-11:30',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('about_settings');
    }
};
