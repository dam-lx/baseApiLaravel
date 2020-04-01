<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SysRoleMapScreen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_role_map_screen', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('role_value')->nullable();
            $table->integer('screen_id')->nullable();
            $table->tinyInteger('is_active')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_role_map_screen');
    }
}
