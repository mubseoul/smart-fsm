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
        Schema::create('w_o_service_tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('wo_id')->default(0);
            $table->integer('service_part_id')->default(0);
            $table->string('service_task')->nullable();
            $table->string('duration')->default(0);
            $table->string('status')->nullable();
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
        Schema::dropIfExists('w_o_service_tasks');
    }
};
