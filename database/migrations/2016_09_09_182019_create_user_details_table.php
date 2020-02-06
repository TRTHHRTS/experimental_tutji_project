<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDetailsTable extends Migration
{

    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->unique();
            $table->tinyInteger('gender')->default(0);
            $table->date('birthday')->nullable();
            $table->string('photo_url')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_details');
    }
}
