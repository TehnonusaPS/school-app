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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('name');                            // e.g. "1-A", "2-D"
            $table->integer('grade');                           // tingkat: 1, 2, 3...
            $table->string('major')->nullable();                // e.g. "MIPA", "IPS"
            $table->string('room')->nullable();                 // e.g. "R. 101"
            $table->string('status')->default('active');        // e.g. "active", "full", "no_teacher"
            $table->foreignUuid('homeroom_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('capacity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
