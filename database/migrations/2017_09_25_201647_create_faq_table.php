<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaqTable extends Migration
{

    public function up()
    {
        Schema::create('faq', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('question_ru', 1024);
            $table->string('question_en', 1024);
            $table->text('answer_ru');
            $table->text('answer_en');
        });
    }

    public function down()
    {
        Schema::dropIfExists('faq');
    }
}
