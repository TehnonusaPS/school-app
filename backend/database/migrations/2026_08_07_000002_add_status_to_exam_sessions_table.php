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
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->enum('status', ['draft', 'published'])->default('draft')->after('notes');
            $table->index(['academic_calendar_event_id', 'status'], 'exam_sessions_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropIndex('exam_sessions_status_index');
            $table->dropColumn('status');
        });
    }
};
