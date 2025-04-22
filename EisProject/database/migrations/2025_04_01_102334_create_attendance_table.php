<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lecturer_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('student_id');
            $table->string('topic');
            $table->enum('category', ['lesson', 'seminar', 'lab']);
            $table->integer('hours_attended');
            $table->integer('number_hours');
            $table->integer('week');
            $table->date('date');
            $table->timestamps();
            $table->foreign('lecturer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendaces');
    }
};
