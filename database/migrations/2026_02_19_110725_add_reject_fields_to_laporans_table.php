<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            //
            $table->boolean('is_rejected')->default(false)->after('is_validated');
            $table->timestamp('rejected_at')->nullable()->after('is_rejected');
            $table->text('rejected_note')->nullable()->after('rejected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            //
            $table->dropColumn(['is_rejected', 'rejected_at', 'rejected_note']);
        });
    }
};
