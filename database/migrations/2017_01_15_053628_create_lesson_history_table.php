<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonHistoryTable extends Migration
{

    public function up()
    {
        Schema::create('lesson_history', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('lesson_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('price');
            $table->string('lesson_name', 500);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lesson_history');
    }
}
