<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonReservedTimes extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_reserved_times', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('lesson_id');
            $table->date('lesson_date');
            $table->string('lesson_time', 5);
            $table->string('duration', 4);
            $table->boolean('closed')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lesson_reserved_times');
    }
}
