<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infrastructures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('facility'); // 'facility' or 'inventory'
            $table->string('name');
            $table->integer('quantity')->default(1);
            $table->string('condition')->nullable(); // e.g. 'Baik', 'Rusak', 'Perlu Perbaikan'
            $table->integer('year_acquired')->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infrastructures');
    }
};
