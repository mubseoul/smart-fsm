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
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Business Information
            $table->string('business_type')->nullable();
            $table->text('service_area')->nullable(); // Can store JSON or comma-separated areas
            $table->string('logo')->nullable(); // Path to logo file
            $table->text('bio')->nullable();
            $table->string('business_name')->nullable(); // Company/Business name
            $table->string('business_phone')->nullable(); // Business contact number
            $table->string('business_address')->nullable(); // Business address
            $table->string('website')->nullable(); // Business website
            
            // Registration Process Tracking
            $table->boolean('is_completed')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_profiles');
    }
};
