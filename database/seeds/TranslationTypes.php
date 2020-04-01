<?php

use Illuminate\Database\Seeder;

class TranslationTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Core\Dao\SDB::table('sys_translate_type')->insert([
            array(
                'code' => "backend",
                'comment' => "backend",
                'order_value'=>'1'
            ),
            array(
                'code' => "common",
                'comment' => "common",
                'order_value'=>'2'
            ),
            array(
                'code' => "api",
                'comment' => "api",
                'order_value'=>'3'
            ),
            array(
                'code' => "validation",
                'comment' => "validation",
                'order_value'=>'4'
            ),
            array(
                'code' => "acl",
                'comment' => "acl",
                'order_value'=>'5'
            ),
            array(
                'code' => "auth",
                'comment' => "auth",
                'order_value'=>'6'
            ),
        ]);
    }
}
