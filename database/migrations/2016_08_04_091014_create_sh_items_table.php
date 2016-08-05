<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sh_items', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('nid')->unsigned()->default(0)->comment('socialhub post id');
            $table->integer('tid')->unsigned()->default(0)->comment('socialhub brand id');
            $table->string('name', 255)->comment('socialhub brand name');
            $table->string('author_name', 255);
            $table->text('raw_body');
            $table->string('social_channel', 255);
            $table->string('post_type', 255)->comment('socialhub post type');
            $table->string('url', 255)->comment('social media source url');
            $table->integer('status')->default(1);
            $table->timestamp('created');
            $table->string('img', 255)->nullable();
            $table->integer('img_width')->unsigned()->default(0);
            $table->integer('img_height')->unsigned()->default(0);
            $table->text('extra_data')->nullable()->comment('dmp details in json format');
            $table->text('tag')->nullable()->comment('tagging from socialhub');
            $table->string('post_language', 50)->nullable();
            $table->boolean('is_linked')->default(0);
            $table->timestamps();
            
            $table->index(['nid', 'tid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sh_items');
    }
}
