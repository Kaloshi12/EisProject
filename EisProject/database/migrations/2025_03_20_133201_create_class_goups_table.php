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
        Schema::create('class_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('nr_max_student')->nullable();
            $table->integer('year_study');
            $table->unsignedBigInteger('degree_id');
            $table->timestamps();
            $table->foreign('degree_id')->references('id')->on('degrees');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_goups');
    }
};
