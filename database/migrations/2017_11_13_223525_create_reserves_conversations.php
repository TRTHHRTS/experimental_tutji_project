<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservesConversations extends Migration {
    public function up(){
        Schema::create('lesson_messages', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('reserve_id');
            $table->string('message', 1024);
            $table->timestamps();
        });
    }

    public function down(){
        Schema::dropIfExists('lesson_messages');
    }
}
