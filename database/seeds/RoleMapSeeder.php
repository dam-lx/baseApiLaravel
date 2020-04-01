<?php

use Illuminate\Database\Seeder;

class RoleMapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ec_role = \App\Core\Dao\SDB::table("plg_customerrank_dtb_customer_rank")
                                    ->where("del_flg",\App\Core\Common\SysConst::NOT_DEL_FLG)
                                    ->pluck("customer_rank_id");
        if(!empty($ec_role)){
            foreach ($ec_role as $obj){
                $arrRole[] = ["ec_role_value" => $obj,"role_value" => \App\Core\Common\RoleConst::NormalUser];
            }
            \App\Core\Dao\SDB::table('sys_role_map_ec_role')->insert($arrRole);
        }
    }
}
