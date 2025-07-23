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
        Schema::table('business_profiles', function (Blueprint $table) {
            // Drop old service_area column
            $table->dropColumn('service_area');
            
            // Add new service location columns
            $table->string('service_country')->nullable()->after('business_type');
            $table->string('service_zipcode')->nullable()->after('service_country');
            $table->string('service_city')->nullable()->after('service_zipcode');
            $table->string('service_address')->nullable()->after('service_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            // Drop new service location columns
            $table->dropColumn(['service_country', 'service_zipcode', 'service_city', 'service_address']);
            
            // Restore old service_area column
            $table->text('service_area')->nullable()->after('business_type');
        });
    }
}; 