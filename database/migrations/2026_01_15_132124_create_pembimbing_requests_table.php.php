<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembimbing_requests', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dosen');
            $table->string('nidn')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('instansi');
            $table->string('prodi');
            $table->string('fakultas')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->unsignedBigInteger('mahasiswa_id');
            $table->foreign('mahasiswa_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('pembimbing_requests');
    }
};
