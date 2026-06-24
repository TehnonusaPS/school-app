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
        Schema::create('foundation_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('foundation_id')->constrained('foundations')->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            $table->enum('status', ['active', 'expired', 'cancelled', 'trial'])->default('active');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foundation_subscriptions');
    }
};
