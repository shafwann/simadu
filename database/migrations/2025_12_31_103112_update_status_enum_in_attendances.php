<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The initial attendance migration already creates the final status set
        // used by the application. The previous raw SQL here only worked on
        // MySQL and fails on PostgreSQL / Laravel Cloud.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
