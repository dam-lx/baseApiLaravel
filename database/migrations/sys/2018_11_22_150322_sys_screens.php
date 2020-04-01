<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SysScreens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_screens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('module',45)->nullable();
            $table->string('controller',45)->nullable();
            $table->string('action',45)->nullable();
            $table->string('screen_code',100)->nullable();
            $table->string('description',100)->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_screens');
    }
}
