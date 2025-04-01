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
        Schema::create('grades_', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id'); // Foreign key to 'student' table
            $table->unsignedBigInteger('course_id'); // Foreign key to 'courses' table
            $table->string('type'); // Type of grade (e.g., exam, assignment, project)
            $table->float('weight'); // Weighted grade for the course, nullable if not yet assigned
            $table->float('points'); // Grade for the course, nullable if not yet assigned
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade'); // Foreign key to 'users' table
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade'); // Foreign key to 'courses' table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades_');
    }
};
