<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonImagesTable extends Migration
{

    public function up()
    {
        Schema::create('lesson_images', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('lesson_id');
            $table->string('url')->unique();
            $table->string('name')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lesson_images');
    }
}
