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
        Schema::create('course_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->enum('day',['monday','tuesday','wednesday','thursday','friday']);
            $table->time('start_hour');
            $table->integer('num_hours');
            $table->enum('category',['theory','lab','seminar']);
            $table->unsignedBigInteger('class_group_id');
            $table->timestamps();
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('class_group_id')->references('id')->on('class_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * 
     */
    public function down(): void
    {
        Schema::dropIfExists('course_hours');
    }
};
