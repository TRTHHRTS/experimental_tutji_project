<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialDataTable extends Migration
{

    public function up()
    {
        Schema::create('social_data', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('id_user')->unique();
            $table->string('id_fb')->unique()->nullable();
            $table->string('id_google')->unique()->nullable();
            $table->string('id_vk')->unique()->nullable();
            $table->string('last_login_via', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('social_data');
    }
}
