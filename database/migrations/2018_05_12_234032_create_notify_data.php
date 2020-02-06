<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotifyData extends Migration
{
    public function up()
    {
        Schema::create('notify_data', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type');
            $table->boolean('sent')->default(false);
            $table->unsignedInteger('user_id');
            $table->string('param', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notify_data');
    }
}
