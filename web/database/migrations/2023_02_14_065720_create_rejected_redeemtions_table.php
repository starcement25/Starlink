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
        Schema::create('rejected_redeemtions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('redeemtion_id');
            $table->bigInteger('user_id');
            $table->bigInteger('point_credited');

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
        Schema::dropIfExists('rejected_redeemtions');
    }
};
