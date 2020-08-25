<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOrganization extends Migration
{

    public function up()
    {
        Schema::create('organization', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',150);
            $table->string('abbreviation',45);
            $table->string('phone',45);
            $table->string('email',45);
            $table->double('x');
            $table->double('y');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('user', function (Blueprint $table) {
            $table->foreign('organization_id')
              ->references('id')
              ->on('organization');
        });

    }

    public function down(){
        Schema::dropIfExists('organization');
    }
}
