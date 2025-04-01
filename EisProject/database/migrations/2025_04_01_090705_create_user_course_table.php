<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_course', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Foreign key to 'users' table
            $table->unsignedBigInteger('course_id'); // Foreign key to 'courses' table
            $table->float('final_grade')->nullable(); // Final grade for the course, nullable if not yet assigned
            $table->enum('status', ['enrolled', 'complete', 'failed'])->default('enrolled');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Foreign key to 'users' table 
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade'); // Foreign key to 'courses' table
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('user_course');
    }
    
};
