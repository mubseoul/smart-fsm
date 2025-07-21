<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->integer('trial_enabled')->default(1)->after('enabled_logged_history')->comment('1 = enabled, 0 = disabled');
            $table->integer('trial_days')->default(30)->after('trial_enabled')->comment('Number of trial days');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['trial_enabled', 'trial_days']);
        });
    }
};
