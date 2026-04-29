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
        Schema::create('pharmacy_ads', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->text('description')->nullable();
            $table->string('ad_message'); // The actual SMS content
            $table->integer('target_week_start')->default(1);
            $table->integer('target_week_end')->default(40);
            $table->string('image_path')->nullable(); // For future UI use
            $table->boolean('is_active')->default(true);
            $table->integer('total_sent')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_ads');
    }
};
