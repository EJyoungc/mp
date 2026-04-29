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
        // Add trimester_id to pharmacy_ads
        Schema::table('pharmacy_ads', function (Blueprint $table) {
            if (! Schema::hasColumn('pharmacy_ads', 'trimester_id')) {
                $table->foreignId('trimester_id')->nullable()->constrained('trimesters')->onDelete('set null');
            }
        });

        // Add location fields to message_histories for tracking reached locations
        Schema::table('message_histories', function (Blueprint $table) {
            if (! Schema::hasColumn('message_histories', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable();
            }
            if (! Schema::hasColumn('message_histories', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message_histories', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('pharmacy_ads', function (Blueprint $table) {
            $table->dropColumn(['trimester_id']);
        });
    }
};
