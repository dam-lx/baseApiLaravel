<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Users extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('email',100);
                $table->string('password',255);
                $table->string('name');
                $table->tinyInteger('role_value');
                $table->string('remember_token',255);
                $table->dateTime('created_date')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->tinyInteger('is_active')->nullable();
                $table->string('tel', 12);
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
        Schema::dropIfExists('users');
    }
}
