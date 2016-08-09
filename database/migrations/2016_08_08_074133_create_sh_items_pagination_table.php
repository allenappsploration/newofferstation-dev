<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShItemsPaginationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sh_items_pagination', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->String('next_url', 255);
            $table->boolean('is_processed')->default(0);
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
        Schema::drop('sh_items_pagination');
    }
}
