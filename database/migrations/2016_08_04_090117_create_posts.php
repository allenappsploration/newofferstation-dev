<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_types_id');
            $table->integer('sh_items_id');
            $table->string('brand', 100);
            $table->string('title', 64);
            $table->text('desc', 255);
            $table->enum('date_type', ['s_n', 'wsl', 'end', 'none'])->default('s_n');
            $table->date('start');
            $table->date('end');
            $table->date('publish_start');
            $table->date('publish_end');
            $table->string('product_url', 255);
            $table->text('keywords');
            $table->enum('approval_status', ['pending', 'publish', 'unpublish']);
            $table->integer('cat_id');
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
        Schema::drop('posts');
    }
}
