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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignUuid('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('time_slot_id')->constrained('time_slots')->cascadeOnDelete();
            $table->tinyInteger('day_of_week'); // 1 = Senin, 2 = Selasa, ..., 6 = Sabtu, 7 = Minggu
            $table->timestamps();

            // Unique constraint: one class cannot have two subjects at the same time slot on the same day
            $table->unique(['academic_year_id', 'classroom_id', 'time_slot_id', 'day_of_week'], 'schedule_classroom_unique');
            
            // Index for teacher conflicts validation
            $table->index(['academic_year_id', 'teacher_id', 'time_slot_id', 'day_of_week'], 'schedule_teacher_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
