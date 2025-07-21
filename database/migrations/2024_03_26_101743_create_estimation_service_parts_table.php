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
        Schema::create('estimation_service_parts', function (Blueprint $table) {
            $table->id();
            $table->integer('estimation_id')->default(0);
            $table->integer('service_part_id')->default(0);
            $table->integer('quantity')->default(0);
            $table->float('amount')->default(0);
            $table->string('type')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('estimation_service_parts');
    }
};
