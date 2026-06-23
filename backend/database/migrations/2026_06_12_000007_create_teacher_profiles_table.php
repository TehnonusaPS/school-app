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
        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nik')->nullable();
            $table->string('nip_nuptk')->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('religion')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('last_education')->nullable();
            $table->string('front_title')->nullable();         // gelar_depan
            $table->string('back_title')->nullable();           // gelar_belakang
            $table->text('address')->nullable();
            $table->string('position')->nullable();             // jabatan
            $table->string('employment_status')->nullable();    // status_kepegawaian
            $table->date('join_date')->nullable();               // tanggal_masuk
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_profiles');
    }
};
