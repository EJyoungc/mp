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
        Schema::create('ad_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mother_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pharmacy_ad_id')->constrained('pharmacy_ads')->onDelete('cascade');
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('message');
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->json('api_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_histories');
    }
};
