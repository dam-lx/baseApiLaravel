<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SysRoleMapEcRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sys_role_map_ec_role')) {
            Schema::create('sys_role_map_ec_role', function (Blueprint $table) {
                $table->integer('ec_role_value')->nullable();
                $table->integer('role_value')->nullable();
                $table->integer('del_flg')->nullable()->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('sys_role_map_ec_role');
    }
}
