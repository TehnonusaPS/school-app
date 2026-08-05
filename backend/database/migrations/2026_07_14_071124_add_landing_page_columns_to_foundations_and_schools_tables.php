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
        Schema::table('foundations', function (Blueprint $table) {
            $table->boolean('landing_page_enabled')->default(false)->after('logo');
            $table->string('landing_page_theme')->nullable()->after('landing_page_enabled');
            $table->jsonb('landing_page_config')->nullable()->after('landing_page_theme');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->boolean('landing_page_enabled')->default(false)->after('logo');
            $table->string('landing_page_theme')->nullable()->after('landing_page_enabled');
            $table->jsonb('landing_page_config')->nullable()->after('landing_page_theme');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('foundations', function (Blueprint $table) {
            $table->dropColumn(['landing_page_enabled', 'landing_page_theme', 'landing_page_config']);
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['landing_page_enabled', 'landing_page_theme', 'landing_page_config']);
        });
    }
};
