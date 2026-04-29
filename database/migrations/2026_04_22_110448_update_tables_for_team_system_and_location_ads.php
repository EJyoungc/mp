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
        // Update organizations table
        Schema::table('organizations', function (Blueprint $table) {
            if (! Schema::hasColumn('organizations', 'owner_id')) {
                $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            }
            if (! Schema::hasColumn('organizations', 'is_pharmacy')) {
                $table->boolean('is_pharmacy')->default(false);
            }
            if (! Schema::hasColumn('organizations', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable();
            }
            if (! Schema::hasColumn('organizations', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable();
            }
        });

        // Update users table
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable();
            }
            if (! Schema::hasColumn('users', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable();
            }
        });

        // Update pharmacy_ads table
        Schema::table('pharmacy_ads', function (Blueprint $table) {
            if (! Schema::hasColumn('pharmacy_ads', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('cascade');
            }
            if (! Schema::hasColumn('pharmacy_ads', 'radius_km')) {
                $table->integer('radius_km')->default(50);
            }
            if (! Schema::hasColumn('pharmacy_ads', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable();
            }
            if (! Schema::hasColumn('pharmacy_ads', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable();
            }
        });

        // Create team_invitations table
        Schema::create('team_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->string('role')->nullable();
            $table->string('token')->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_invitations');

        Schema::table('pharmacy_ads', function (Blueprint $table) {
            $table->dropColumn(['organization_id', 'radius_km', 'latitude', 'longitude']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['owner_id', 'is_pharmacy', 'latitude', 'longitude']);
        });
    }
};
