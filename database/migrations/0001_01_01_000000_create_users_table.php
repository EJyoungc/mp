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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->integer('role_id')->default(5);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            // mother detailes
            $table->date('date_of_birth')->nullable();
            $table->integer('age')->nullable();
            $table->string('religion')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('level_of_education')->nullable();
            $table->string('occupation')->nullable();
            $table->string('next_of_kin')->nullable();
            $table->string('next_of_kin_mobile')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();


            // PHYSICAL DEFORMITY Detailes

            $table->integer('height')->nullable()->comment('Height in centimeters');
            $table->enum('leg_or_spine', ['yes', 'no'])->default('no')->comment('Leg or spine issues');
            $table->enum('deformity', ['yes', 'no'])->default('no')->comment('Any deformity');

            //Previous Obstetric History
            $table->integer('deliveries')->default(0)->comment('Number of deliveries');
            $table->integer('abortions')->default(0)->comment('Number of abortions');
            $table->enum('still_births', ['yes', 'no'])->nullable()->comment('Still births status');
            $table->enum('c_section', ['yes', 'no'])->nullable()->comment('Abnormal delivery: C-sections');
            $table->enum('vacum', ['yes', 'no'])->nullable()->comment('Abnormal delivery: Vacuum');

           
            $table->enum('multiple', ['yes', 'no'])->nullable();
            $table->enum('aph', ['yes', 'no'])->nullable();
            $table->enum('pph', ['yes', 'no'])->nullable();
            $table->enum('pre_eclampsia', ['yes', 'no'])->nullable();
            $table->enum('tuberculosis', ['yes', 'no'])->nullable();
            $table->enum('asthma', ['yes', 'no'])->nullable();
            $table->enum('hypertension', ['yes', 'no'])->nullable();
            $table->enum('diabetes', ['yes', 'no'])->nullable();
            $table->enum('epilepsy', ['yes', 'no'])->nullable();
            $table->enum('renal_disease', ['yes', 'no'])->nullable();
            $table->enum('fistula_repair', ['yes', 'no'])->nullable();
            $table->enum('menstrual_cycle', ['regular', 'abnormal'])->nullable();

            $table->string('traditional_authority')->nullable();
           

            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
