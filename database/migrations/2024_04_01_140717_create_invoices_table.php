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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('client')->default(0);
            $table->integer('wo_id')->default(0);
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->integer('invoice_id')->default(0);
            $table->float('total')->default(0);
            $table->float('discount')->default(0);
            $table->string('status')->nullable();
            $table->integer('parent_id')->default(0);
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('invoices');
    }
};
