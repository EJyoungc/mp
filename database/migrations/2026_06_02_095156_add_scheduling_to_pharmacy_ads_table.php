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
        Schema::table('pharmacy_ads', function (Blueprint $table) {
            $table->string('schedule_type')->default('daily'); // daily, weekly, monthly
            $table->integer('schedule_limit')->default(1);     // how many times in that period
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pharmacy_ads', function (Blueprint $table) {
            $table->dropColumn(['schedule_type', 'schedule_limit']);
        });
    }
};
