<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOrganization extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',150);
            $table->string('abbreviation',45);
            $table->string('phone',45);
            $table->string('email',45);
            $table->integer('status');
            $table->double('x');
            $table->double('y');
            $table->timestamps();
        });

        Schema::create('organization_place', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id')->unsigned();
            $table->integer('place_id')->unsigned();
        });

        Schema::table('organization_place', function (Blueprint $table) {
            $table->foreign('organization_id')
              ->references('id')
              ->on('organization');

            $table->foreign('place_id')
            ->references('id')
            ->on('place');
        });

        Schema::table('user', function (Blueprint $table) {
            $table->foreign('organization_id')
              ->references('id')
              ->on('organization');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organization');
    }
}
