<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSettingsTable extends Migration
{

    public function up()
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('user_id');
            $table->boolean('notify_new_messages')->default(false);
            $table->boolean('notify_new_lesson_reviews')->default(false);
            $table->boolean('notify_scheduled_lessons')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_settings');
    }
}
