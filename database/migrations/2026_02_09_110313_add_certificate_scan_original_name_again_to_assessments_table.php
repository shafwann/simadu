<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            if (!Schema::hasColumn('assessments', 'certificate_scan_original_name')) {
                $table->string('certificate_scan_original_name')->nullable()->after('certificate_scan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            if (Schema::hasColumn('assessments', 'certificate_scan_original_name')) {
                $table->dropColumn('certificate_scan_original_name');
            }
        });
    }
};
