<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgingTable extends Migration
{

    public function up()
    {
        Schema::create('aging', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->string('name_ru');
            $table->string('name_en');
            $table->string('desc_ru')->nullable();
            $table->string('desc_en')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('aging');
    }
}
