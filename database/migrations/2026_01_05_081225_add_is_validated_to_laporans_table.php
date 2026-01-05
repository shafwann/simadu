<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->boolean('is_validated')->default(false)->after('nama_file');
            $table->timestamp('validated_at')->nullable()->after('is_validated');
        });
    }

    public function down(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->dropColumn(['is_validated', 'validated_at']);
        });
    }
};
