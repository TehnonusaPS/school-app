<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('payment_method'); // cash, bca, mandiri, gopay, dana, ovo, card
            $table->decimal('amount', 15, 2);
            $table->string('reference_number')->unique();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->dateTime('payment_date')->useCurrent();
            $table->foreignUuid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_payments');
    }
};
