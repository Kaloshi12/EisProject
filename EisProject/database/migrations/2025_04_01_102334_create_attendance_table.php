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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id'); // Foreign key to 'users' table
            $table->unsignedBigInteger('course_id'); // Foreign key to 'courses' table
            $table->unsignedBigInteger('course_hour_id'); // Foreign key to 'courses_hours' table
            $table->date('date'); // Date of the attendance
            $table->integer('hours_present'); // Number of hours present
            $table->string('week'); // Week of the semester
        
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade'); // Foreign key to 'users' table
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade'); // Foreign key to 'courses' table
            $table->foreign('course_hour_id')->references('id')->on('courses_hours')->onDelete('cascade'); // Foreign key to 'courses_hours' table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
