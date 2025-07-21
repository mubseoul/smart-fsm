<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('w_o_requests', function (Blueprint $table) {
            $table->id();
            $table->text('request_detail')->nullable();
            $table->integer('client')->default(0);
            $table->integer('asset')->default(0);
            $table->string('priority')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->nullable();
            $table->integer('assign')->default(0);
            $table->text('notes')->nullable();
            $table->date('preferred_date')->nullable();
            $table->string('preferred_time')->nullable();
            $table->text('preferred_note')->nullable();
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
        Schema::dropIfExists('w_o_requests');
    }
};
