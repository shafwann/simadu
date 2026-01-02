<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <--- JANGAN LUPA INI!

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('Hadir', 'Telat', 'Sakit', 'Izin', 'Alpha') NOT NULL DEFAULT 'Hadir'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE attendances MODIFY COLUMN status ENUM('Hadir', 'Telat', 'Alpha') NOT NULL DEFAULT 'Hadir'");
    }
};
