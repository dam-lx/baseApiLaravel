<?php

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Core\Dao\SDB::table('sys_catelory')->insert([
            array(
                'id' => 1,
                'name' => 'root',
                'lft'=>1,
                'rgt'=>2,
                'url'=>''
            )
        ]);
    }
}
