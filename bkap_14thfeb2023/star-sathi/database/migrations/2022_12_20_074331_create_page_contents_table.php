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
        Schema::create('page_contents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('page_id')->nullable();
            $table->string('title')->nullable();
            $table->text('slug')->nullable();
            $table->string('element_type')->nullable();
            $table->string('element_name')->nullable();
            $table->string('element_id')->nullable();
            $table->longText('element_value')->nullable();
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
        Schema::dropIfExists('page_contents');
    }
};
