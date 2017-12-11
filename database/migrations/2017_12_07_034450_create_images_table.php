<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('group_id')->nullable(); // only available if image_type is 1
            $table->string('name');
            $table->integer('likes');
            $table->integer('dislikes');
            $table->integer('image_type'); // 0: PERSONAL, 1: GROUP
            $table->integer('private')->default(0); // 0: NO, 1: FRIENDS, 2: PRIVATE
            $table->string('ext');
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
        Schema::dropIfExists('images');
    }
}
