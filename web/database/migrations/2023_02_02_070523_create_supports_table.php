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
        Schema::create('supports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->nullable();
            $table->text('comment')->nullable();
            $table->tinyInteger('support_type')->comment('1 = Not Delivered, 2= Defective')->nullable();
            $table->string('image_path')->nullable();
            $table->tinyInteger('status')->comment('1 =Pending, 2= Resolved, 3= Rejected')->nullable();
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
        Schema::dropIfExists('supports');
    }
};
