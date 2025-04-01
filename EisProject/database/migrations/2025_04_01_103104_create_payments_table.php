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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id'); // Foreign key to 'users' table
            $table->unsignedBigInteger('degree_id'); // Foreign key to 'degrees' table
            $table->date('payment_date'); // Date of the payment
            $table->float('cost_paid'); // Amount paid
            $table->string('payment_method'); // Method of payment (e.g., credit card, bank transfer)
            $table->string('iban')->nullable(); // IBAN number for bank transfer
            $table->string('swift')->nullable(); // SWIFT code for bank transfer
            $table->string('bank_name')->nullable(); // Name of the bank for bank transfer
            $table->string('currency')->default('EUR'); // Currency of the payment
            $table->string('description')->nullable(); // Description of the payment
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
