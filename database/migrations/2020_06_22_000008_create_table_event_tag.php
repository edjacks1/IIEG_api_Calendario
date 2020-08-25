<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEventTag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_tag', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',50);
            $table->string('color',15);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('event', function (Blueprint $table) {
            $table->foreign('tag')
              ->references('id')
              ->on('event_tag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_tag');
    }
}
