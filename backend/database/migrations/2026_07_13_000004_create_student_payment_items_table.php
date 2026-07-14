<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_payment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_payment_id')->constrained('student_payments')->cascadeOnDelete();
            $table->foreignId('student_bill_id')->constrained('student_bills')->cascadeOnDelete();
            $table->decimal('amount_paid', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_payment_items');
    }
};
