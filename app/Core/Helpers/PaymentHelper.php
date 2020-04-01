<?php


namespace App\Core\Helpers;


use App\Core\Common\SysConst;
use App\Core\Common\TaxRule;
use App\Core\Common\UserConst;
use App\Core\Dao\SDB;
use Illuminate\Support\Facades\Auth;

class PaymentHelper
{
    public static function getPaymentConfig()
    {
        $plg_payment_config = SDB::table("plg_sln_plugin_config")->orderBy("id", "desc")->first();
        if(!empty($plg_payment_config)){
            $payKbnKaisu        = json_decode($plg_payment_config->sub_data);
            return $payKbnKaisu;
        }
        return null;
    }

}