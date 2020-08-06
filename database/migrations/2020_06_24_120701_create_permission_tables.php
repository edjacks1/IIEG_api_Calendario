<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission', function (Blueprint $table) {
            $table->bigIncrements('permission_id');
            $table->string('name',50);
            $table->string('slug',50);
            $table->string('description',250);
            $table->timestamps();
        });

        Schema::create('role', function (Blueprint $table) {
            $table->bigIncrements('role_id');
            $table->string('name',150);
            $table->string('description',250);
            $table->timestamps();
        });

        Schema::create('role_has_permission', function (Blueprint $table) {
            $table->bigIncrements('role_has_permission_id');
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
        });

        Schema::table('role_has_permission', function (Blueprint $table) {
            $table->foreign('permission_id')
              ->references('permission_id')
              ->on('permission')->onDelete('cascade');

            $table->foreign('role_id')
            ->references('role_id')
            ->on('role')->onDelete('cascade');
        });

        Schema::create('user_has_role', function (Blueprint $table) {
            $table->bigIncrements('user_has_role_id');
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('role_id');
        });

        Schema::table('user_has_role', function (Blueprint $table) {
            $table->foreign('user_id')
              ->references('id')
              ->on('user');

              $table->foreign('role_id')
              ->references('role_id')
              ->on('role')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_has_role');
        Schema::dropIfExists('role_has_permission');
        Schema::dropIfExists('role');
        Schema::dropIfExists('permission');
    }
}
