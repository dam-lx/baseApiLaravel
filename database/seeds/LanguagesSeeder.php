<?php

use Illuminate\Database\Seeder;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Core\Dao\SDB::table('sys_languages')->insert([
            array(
                'code' => "en",
                'name' => "English",
                'order'=>'1'
            ),
            array(
                'code' => "jp",
                'name' => "Japanese",
                'order'=>'2'
            )
        ]);
    }
}
