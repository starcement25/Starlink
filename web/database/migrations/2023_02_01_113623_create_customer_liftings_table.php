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
        Schema::create('customer_liftings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('dealer_id')->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->integer('month')->nullable();
            $table->string('year', 25)->nullable();
            $table->decimal('quantity', 12, 2)->nullable();
            $table->boolean('status')->nullable();
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
        Schema::dropIfExists('customer_liftings');
    }
};
