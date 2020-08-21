<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePlace extends Migration
{

    public function up()
    {
        Schema::dropIfExists('place');
        Schema::create('place', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',50);
            $table->double('x');
            $table->double('y');
            $table->string('description',150);
            $table->integer('status');
            $table->integer('organization_id')->unsigned();
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organization');
        });
    }

    public function down()
    {
        Schema::dropIfExists('place');
    }
}
