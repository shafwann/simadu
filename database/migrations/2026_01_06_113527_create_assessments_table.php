<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('certificate_no', 100);

            $table->unsignedTinyInteger('aspek_1');
            $table->unsignedTinyInteger('aspek_2');
            $table->unsignedTinyInteger('aspek_3');
            $table->unsignedTinyInteger('aspek_4');
            $table->unsignedTinyInteger('aspek_5');
            $table->unsignedTinyInteger('aspek_6');

            $table->unsignedSmallInteger('total');
            $table->decimal('average', 5, 2);
            $table->string('grade_letter', 2);

            $table->timestamp('assessed_at')->nullable();

            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
}
