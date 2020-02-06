<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservedLessonsTable extends Migration
{

    public function up()
    {
        Schema::create('reserved_lessons', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('lesson_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('reserved_time_id');
            $table->unsignedTinyInteger('count')->default(1);
            $table->string('lesson_name', 200);
            $table->string('teacher_phone', 10)->nullable();
            $table->unsignedTinyInteger('reserve_status')->default(0);
            $table->string('reason')->nullable();
            $table->boolean('closed')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reserved_lessons');
    }
}
