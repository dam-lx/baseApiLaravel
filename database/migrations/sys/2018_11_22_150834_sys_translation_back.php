<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SysTranslationBack extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_translation_back', function (Blueprint $table) {
            $table->bigIncrements('id_back');
            $table->dateTime('date_back')->nullable();
            $table->string('lang_code',45)->nullable();
            $table->string('translate_type',100)->nullable();
            $table->longText('text')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->tinyInteger('is_deleted')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_translation_back');
    }
}
