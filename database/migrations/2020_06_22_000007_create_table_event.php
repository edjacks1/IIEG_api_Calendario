<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('description', 150);
            $table->softDeletes();
            $table->timestamps();
        });


        Schema::create('event', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('description', 150);
            $table->integer('place_id')->unsigned();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->integer('created_by')->unsigned();
            $table->integer('status');
            $table->softDeletes();
            $table->tinyInteger('resources_check')->default(0);
            $table->integer('type')->unsigned();
            $table->integer('tag')->unsigned();
            $table->timestamps();
        });

        Schema::table('event', function (Blueprint $table) {
            $table->foreign('type')
                ->references('id')
                ->on('event_type');
        });

        Schema::create('event_organizer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->unsigned();
            $table->integer('user_id')->unsigned();
        });


        Schema::table('event_organizer', function (Blueprint $table) {
            $table->foreign('event_id')
                ->references('id')
                ->on('event');

            $table->foreign('user_id')
                ->references('id')
                ->on('user');
        });

        Schema::create('event_guest', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->unsigned();
            $table->integer('guest_id')->unsigned();
        });

        Schema::table('event_guest', function (Blueprint $table) {
            $table->foreign('event_id')
                ->references('id')
                ->on('event');

            $table->foreign('guest_id')
                ->references('id')
                ->on('user');
        });

        Schema::create('event_resource', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->unsigned();
            $table->integer('resource_id')->unsigned();
            $table->boolean('isOk')->nullable();
        });

        Schema::table('event_resource', function (Blueprint $table) {
            $table->foreign('event_id')
                ->references('id')
                ->on('event');

            $table->foreign('resource_id')
                ->references('id')
                ->on('resource');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_type');
        Schema::dropIfExists('event');
        Schema::dropIfExists('event_guest');
        Schema::dropIfExists('event_resource');
        Schema::dropIfExists('event_organizer');
    }
}
