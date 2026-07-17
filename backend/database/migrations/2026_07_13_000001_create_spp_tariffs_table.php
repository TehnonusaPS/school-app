<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spp_tariffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('amount', 15, 2);
            $table->string('type')->default('mandatory'); // mandatory, addon
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spp_tariffs');
    }
};
