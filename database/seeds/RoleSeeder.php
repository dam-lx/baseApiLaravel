<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Core\Dao\SDB::table('sys_roles')->insert([
            array(
                'name' => "Guess",
                'role_value' => \App\Core\Common\RoleConst::PublicRole,
                'description'=>'Guess'
            ),
            array(
                'name' => "System Admin",
                'role_value' => \App\Core\Common\RoleConst::SysAdminRole,
                'description'=>'SysAdminRole'
            ),
            array(
                'name' => "Nomarl User",
                'role_value' => \App\Core\Common\RoleConst::NormalUser,
                'description'=>'Nomarl User'
            ),
            array(
                'name' => "Other Party",
                'role_value' => \App\Core\Common\RoleConst::Manager,
                'description'=>'PartyRole'
            )
        ]);
    }
}
