<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SysTranslateType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_translate_type', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code',45)->nullable();
            $table->text('comment')->nullable();
            $table->integer('order_value')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_translate_type');
    }
}
