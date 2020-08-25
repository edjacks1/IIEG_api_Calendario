<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUsers extends Migration
{

    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',45);
            $table->string('second_name',45)->nullable();
            $table->string('last_name',45);
            $table->string('maternal_surname',45)->nullable();
            $table->string('email',150)->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->integer('organization_id')->unsigned();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user');
    }
}
