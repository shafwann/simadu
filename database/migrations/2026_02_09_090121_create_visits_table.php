<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('visits')) {
            return;
        }

        Schema::create('visits', function (Blueprint $table) {
            $table->id();

            // Info kunjungan
            $table->string('path')->nullable();            // contoh: admin/students
            $table->string('method', 10)->nullable();      // GET/POST dll
            $table->string('ip', 45)->nullable();          // IPv4/IPv6
            $table->string('user_agent', 500)->nullable(); // UA bisa panjang

            // Relasi user (kalau login)
            $table->foreignId('user_id')->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->timestamps();

            // Index biar query cepat
            $table->index(['created_at']);
            $table->index(['path']);
            $table->index(['ip']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
