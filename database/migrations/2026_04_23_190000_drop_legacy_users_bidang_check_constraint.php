<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_bidang_check');
    }

    public function down(): void
    {
        // Constraint lama tidak dipulihkan karena kolom `bidang` sekarang dipakai sebagai string dinamis.
    }
};
