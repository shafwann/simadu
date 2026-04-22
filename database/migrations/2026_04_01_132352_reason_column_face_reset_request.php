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
        if (! Schema::hasTable('face_reset_requests') || Schema::hasColumn('face_reset_requests', 'reason')) {
            return;
        }

        Schema::table('face_reset_requests', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('face_reset_requests') || ! Schema::hasColumn('face_reset_requests', 'reason')) {
            return;
        }

        Schema::table('face_reset_requests', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }
};
