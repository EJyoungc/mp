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
        Schema::create('message_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('tip_id');
            $table->integer('week_id');
            $table->integer('day_range_id');
            $table->integer('day_id');
            $table->integer('mother_id');
            $table->integer('history_id');
            $table->string('message_status')->default('unsent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_histories');
    }
};
