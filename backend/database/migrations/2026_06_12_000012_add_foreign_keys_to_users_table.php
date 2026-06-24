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
        Schema::table('users', function (Blueprint $table) {
            // Add foreign key constraints for columns added in users table
            $table->foreign('role_id')->references('id')->on('roles')->restrictOnDelete();
            $table->foreign('foundation_id')->references('id')->on('foundations')->nullOnDelete();
            $table->foreign('school_id')->references('id')->on('schools')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['foundation_id']);
            $table->dropForeign(['school_id']);
        });
    }
};
