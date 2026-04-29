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
        Schema::table('tips', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('approved'); // Defaulting to approved for existing tips
            $table->boolean('is_template')->default(false);
        });

        Schema::table('histories', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');
        });

        Schema::table('message_histories', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tips', function (Blueprint $table) {
            $table->dropColumn(['organization_id', 'created_by', 'approved_by', 'status', 'is_template']);
        });

        Schema::table('histories', function (Blueprint $table) {
            $table->dropColumn('organization_id');
        });

        Schema::table('message_histories', function (Blueprint $table) {
            $table->dropColumn('organization_id');
        });
    }
};
