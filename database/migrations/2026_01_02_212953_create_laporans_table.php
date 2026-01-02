<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('nama_file');
            $table->timestamps(); // pakai timestamps saja, bisa juga tambah 'uploaded_at' kalau mau
        });
    }

    public function down(): void {
        Schema::dropIfExists('laporans');
    }
};
