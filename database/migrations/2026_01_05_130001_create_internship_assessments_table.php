<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedTinyInteger('kedisiplinan');
            $table->unsignedTinyInteger('etika');
            $table->unsignedTinyInteger('kinerja');
            $table->string('grade', 2);

            $table->timestamps();

            // 1 mahasiswa 1 penilaian akhir
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
