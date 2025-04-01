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
        Schema::create('syllabus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id'); // Foreign key to 'courses' table
            $table->string('topic'); // Title of the syllabus
            $table->text('description'); // Description of the syllabus
            $table->string('resources'); // Resources for the syllabus
            $table->integer('week_number'); // Week of the semester
            $table->integer('hours'); // Number of hours for the syllabus
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade'); // Foreign key to 'courses' table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('syllabus');
    }
};
