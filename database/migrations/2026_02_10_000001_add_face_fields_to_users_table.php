<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFaceFieldsToUsersTable extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('face_descriptor')->nullable()->after('remember_token');
            $table->string('face_image_path')->nullable()->after('face_descriptor');
            $table->boolean('face_2fa_enabled')->default(false)->after('face_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['face_descriptor', 'face_image_path', 'face_2fa_enabled']);
        });
    }
}
