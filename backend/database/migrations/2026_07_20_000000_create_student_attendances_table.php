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
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained('student_profiles')->cascadeOnDelete();
            $table->date('date');
            $table->time('time_in');
            $table->time('time_out')->nullable();
            $table->enum('status', ['H', 'T', 'I', 'S', 'A']);
            $table->string('verification_method'); // 'kamera', 'rfid', 'fingerprint'
            $table->string('photo_path')->nullable();
            $table->timestamps();

            // Index
            $table->index(['student_profile_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};
