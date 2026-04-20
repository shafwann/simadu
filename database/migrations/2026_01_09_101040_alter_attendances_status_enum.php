<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // No-op: attendances.status is already created with this exact set of
        // values in the original table migration. Keeping this migration empty
        // avoids database-specific enum alteration issues on PostgreSQL.
    }

    public function down(): void
    {
        //
    }
};
