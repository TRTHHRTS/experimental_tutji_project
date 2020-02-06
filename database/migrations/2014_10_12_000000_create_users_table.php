<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{

    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('password')->nullable();
            $table->string('email')->unique()->nullable();
            $table->tinyInteger('email_verified')->default(0);
            $table->string('email_token')->nullable();
            $table->string('phone', 10)->nullable();
            $table->tinyInteger('is_phone_confirmed')->default(0);
            $table->boolean('blocked')->default(false);
            $table->string('blocked_desc')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('user_role', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->unique();
            $table->boolean('moder_rights')->default(false);
            $table->boolean('admin_rights')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('users');
    }
}
