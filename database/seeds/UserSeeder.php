<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Core\Dao\SDB::table('users')->insert([
            array(
                'name' => 'admin',
                'email'=>'admin@mail',
                'password'=>\Illuminate\Support\Facades\Hash::make('123456'),
                'role_value'=>\App\Core\Common\RoleConst::SysAdminRole,
                'is_active'=>1,
                'remember_token'=>''
            )
        ]);
    }
}
