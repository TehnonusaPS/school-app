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
        Schema::create('foundations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->date('established_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'trial'])->default('active');
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('deed_number')->nullable();       // no_akta
            $table->date('deed_date')->nullable();            // tanggal_akta
            $table->string('decree_number')->nullable();      // no_sk
            $table->date('decree_date')->nullable();           // tanggal_sk
            $table->string('logo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foundations');
    }
};
