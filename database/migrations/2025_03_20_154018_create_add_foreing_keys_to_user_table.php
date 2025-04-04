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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->after('bourse_percentage');
            $table->unsignedBigInteger('department_id')->nullable()->after('supervised_id');
            $table->unsignedBigInteger('degree_id')->nullable()->after('department_id');
            $table->unsignedBigInteger('group_id')->nullable()->after('degree_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('degree_id')->references('id')->on('degrees')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('class_groups')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');  // Corrected typo here
        });
    }
    
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('role_id');
            $table->dropForeign('group_id');
            $table->dropForeign('degree_id');
            $table->dropForeign('department_id');  
        });
    }
    
};
 