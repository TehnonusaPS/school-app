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
        Schema::create('student_warning_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('foundation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal_dibuat');
            $table->string('jenis_surat'); // 'Surat Pelanggaran' / 'Surat Tunggakan'
            $table->string('nama');
            $table->string('nisn');
            $table->string('kelas');
            $table->date('tanggal')->nullable(); // Tanggal pelanggaran
            $table->text('perihal_pelanggaran')->nullable();
            $table->string('jumlah_tunggakan')->nullable();
            $table->string('status')->default('Selesai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_warning_certificates');
    }
};
