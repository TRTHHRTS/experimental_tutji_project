<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{

    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('user_id');
            $table->string('name', 200)->nullable();
            $table->string('short_desc', 200)->nullable();
            $table->text('description')->nullable();
            // Геоданные
            $table->string('country_code', 2)->nullable();
            $table->integer('state_code')->default(0);
            $table->integer('city_code')->default(0);
            $table->string('city_name', 50)->nullable();
            $table->string('address', 200)->nullable();

            $table->tinyInteger('aging_id')->nullable();
            $table->tinyInteger('rule_id')->nullable();
            // Статус - по-умолчанию 0 (СОЗДАН)
            $table->tinyInteger('status')->default(0);
            $table->boolean('equipment_have_all')->default(true);
            $table->text('equipment_have_all_desc')->nullable();
            $table->boolean('equipment_first_aid')->default(false);
            $table->boolean('equipment_memo_security')->default(false);
            $table->boolean('equipment_extinguisher')->default(false);
            // координаты на карте
            $table->double('lat', 20, 15)->nullable();
            $table->double('lng', 20, 15)->nullable();
            // настройки цен
//            $table->boolean('is_price_for_hour')->default(false);
            $table->unsignedInteger('price')->nullable();
            // прочие настройки урока
            $table->tinyInteger('pupil_count')->default(3);
            $table->boolean('is_unique')->default(false);
            $table->boolean('is_favorite')->default(false);
            $table->integer('moder_id_who_approved')->nullable();

            $table->index('name');

            $table->timestamps();
        });

        Schema::create('recommended_lessons_users', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('lesson_id');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('recommended_lessons_users');
        Schema::dropIfExists('lessons');
    }
}
