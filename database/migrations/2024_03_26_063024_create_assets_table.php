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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('asset_number')->nullable();
            $table->integer('part')->default(0);
            $table->integer('parent_asset')->default(0);
            $table->string('giai')->nullable();
            $table->date('order_date')->nullable();
            $table->date('installation_date')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiration')->nullable();
            $table->text('warranty_notes')->nullable();
            $table->text('description')->nullable();
            $table->integer('parent_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assets');
    }
};
