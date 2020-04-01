<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UsersDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->integer('gender')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('avatar', 255)->nullable();
            $table->integer('postcode')->nullable();
            $table->string('job')->nullable();
            $table->string('fax')->nullable();
            $table->string('company_name', 100)->nullable();
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('first_name_kana', 50)->nullable();
            $table->string('last_name_kana', 50)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('address_number', 50)->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_detail');
    }
}
