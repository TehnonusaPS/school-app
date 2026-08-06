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
        Schema::table('academic_years', function (Blueprint $table) {
            $table->string('calendar_status')->default('draft');
            $table->text('calendar_rejected_reason')->nullable();
            $table->timestamp('calendar_submitted_at')->nullable();
            $table->timestamp('calendar_reviewed_at')->nullable();
            $table->foreignUuid('calendar_reviewed_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropForeign(['calendar_reviewed_by']);
            $table->dropColumn([
                'calendar_status',
                'calendar_rejected_reason',
                'calendar_submitted_at',
                'calendar_reviewed_at',
                'calendar_reviewed_by'
            ]);
        });
    }
};
