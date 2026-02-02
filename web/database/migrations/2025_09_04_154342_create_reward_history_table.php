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
        Schema::create('reward_history', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('reward_id');
            $table->integer('point');
            $table->integer('bag');
            $table->unsignedBigInteger('lifting_id');
            $table->unsignedBigInteger('user_id');
            $table->date('date')->nullable();
            $table->unsignedTinyInteger('is_verified')->comment('0 = unverified, 1 = verified, 2 = rejected');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_by_at')->nullable();
            $table->unsignedTinyInteger('is_bonus')->default(0)->comment('0 = no, 1 = yes');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('show_point')->default(1)->comment('0 = no, 1 = yes');
            $table->string('attachment')->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('reward_history');
    }
};
