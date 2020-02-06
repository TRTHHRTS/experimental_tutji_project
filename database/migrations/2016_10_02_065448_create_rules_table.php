<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRulesTable extends Migration
{

    public function up()
    {
        Schema::create('lesson_rules', function (Blueprint $table) {
            $table->unsignedInteger('lesson_id')->primary()->unique();
            $table->boolean('animals')->default(false);
            $table->boolean('allow_smoking')->default(false);
            $table->boolean('confirm_email')->default(true);
            $table->boolean('confirm_phone')->default(true);
            $table->boolean('profile_photo')->default(true);
            $table->string('added_info', 2048)->default("");
        });
    }

    public function down()
    {
        Schema::dropIfExists('lesson_rules');
    }
}
