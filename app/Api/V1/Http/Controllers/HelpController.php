<?php

namespace App\Api\V1\Http\Controllers;

use App\Core\Dao\SDB;

class HelpController extends Controller
{
    public function agreement()
    {
        return view('api.agreement');
    }
    public function about()
    {
        $baseInfo = SDB::table('dtb_base_info')->first();
        return view('api.about', ['baseInfo' => $baseInfo]);
    }
    public function privacy()
    {
        $baseInfo = SDB::table('dtb_base_info')->select('company_name')->first();
        return view('api.privacy', ['baseInfo' => $baseInfo]);
    }
    public function tradelaw()
    {
        $baseInfo = SDB::table('dtb_help')->first();
        return view('api.tradelaw', ['help' => $baseInfo]);
    }
}
