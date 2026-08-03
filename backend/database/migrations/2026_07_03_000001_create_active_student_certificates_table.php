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
        Schema::create('active_student_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('foundation_id')->constrained('foundations')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignUuid('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('semester');
            $table->string('nama');
            $table->string('nisn');
            $table->string('kelas');
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('status')->default('Selesai');
            $table->date('tanggal_dibuat');
            $table->timestamps();

            // Indexes for faster querying
            $table->index('foundation_id');
            $table->index('school_id');
            $table->index('student_id');
            $table->index('academic_year_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_student_certificates');
    }
};
