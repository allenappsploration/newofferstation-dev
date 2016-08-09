<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostImgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_imgs', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('posts_id')->unsigned();
            $table->float('ratio');
            $table->String('path', 255);
            $table->timestamps();
            
            $table->foreign('posts_id')->references('id')->on('posts');
            $table->index(['posts_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('post_imgs');
    }
}
