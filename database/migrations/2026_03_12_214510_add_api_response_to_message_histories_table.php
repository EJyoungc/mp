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
        Schema::table('message_histories', function (Blueprint $table) {
            $table->json('api_response')->nullable()->after('message_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message_histories', function (Blueprint $table) {
            $table->dropColumn('api_response');
        });
    }
};
