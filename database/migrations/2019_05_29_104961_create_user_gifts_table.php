<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserGiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('user_gifts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('gift_type_id')->unsigned();
            $table->integer('prize_type_id')->unsigned()->nullable();
            $table->integer('value')->nullable();
            $table->enum('status', config('selectOptions.user_gifts.status'));
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('gift_type_id')->references('id')->on('gift_types')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('prize_type_id')->references('id')->on('prize_types')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_gifts');
    }
}
