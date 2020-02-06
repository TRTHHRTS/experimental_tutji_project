<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbackTable extends Migration
{

    public function up()
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('content', 2000);
            $table->string('user_email', 100)->nullable();
            $table->string('answer', 2000)->nullable();
            $table->unsignedInteger('moder_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('feedback');
    }
}
