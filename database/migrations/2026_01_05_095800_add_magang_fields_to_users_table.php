<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nim')->after('email');
            $table->string('instansi_asal')->after('nim');
            $table->date('tanggal_masuk')->after('instansi_asal');
            $table->date('tanggal_selesai')->after('tanggal_masuk');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('tanggal_selesai');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nim',
                'instansi_asal',
                'tanggal_masuk',
                'tanggal_selesai',
                'status'
            ]);
        });
    }
};
