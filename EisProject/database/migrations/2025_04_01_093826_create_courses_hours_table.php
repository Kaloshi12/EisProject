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
        Schema::create('courses_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id'); // Foreign key to 'courses' table
            $table->enum('type', ['lecture', 'lab']); // Type of course hour
            $table->integer('week'); // Week of the semester
            $table->string('start_time'); // Start time of the course hour
            $table->integer('hours'); // Number of hours for the course hour
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade'); // Foreign key to 'courses' table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses_hours');
    }
};
