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
        Schema::create('activity_news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('foundation_id')->constrained('foundations')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->string('category')->default('UMUM'); // UMUM, AKADEMIK, KEUANGAN
            $table->longText('image')->nullable(); // For base64 / URL of image
            $table->date('publish_date');
            $table->timestamps();
            $table->softDeletes();

            $table->index('foundation_id');
            $table->index('school_id');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_news');
    }
};
