<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsCodesTable extends Migration
{
    public function up()
    {
        Schema::create('sms_codes', function (Blueprint $table) {
            $table->increments('id')->unique();
            $table->unsignedInteger('user_id');
            $table->string('phone', 11);
            $table->string('message', 300);
            $table->integer('code');
            $table->unsignedInteger('sms_id');
            $table->decimal('price', 4, 2);
            $table->string('currency', 3);
            $table->boolean('active')->default(true);
            $table->boolean('confirmed')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sms_codes');
    }
}
