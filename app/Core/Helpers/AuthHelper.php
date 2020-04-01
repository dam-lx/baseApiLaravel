<?php

namespace App\Core\Helpers;

use App\Core\Common\RoleConst;
use App\Core\Common\SysConst;
use App\Core\Common\UserConst;
use App\Core\Dao\SDB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class AuthHelper
{
    const ENABLE_ROUTE = 1;
    const DISABLE_ROUTE = 0;

    /**
     * @param $routerName
     * @return bool
     */
    public static function IsAccess($routerName)
    {
        $roleId = RoleConst::PublicRole;
        if (Auth::check()) {
            $roleId = Auth::user()->role_value;
        }
        $moduleInfor = CommonHelper::getModuleInforByRouter($routerName);
        if (self::isAccessByRole($roleId, $moduleInfor->screenCode) == true) {
            return true;
        }
        return false;
    }

    /*
     * Check access multi route
     * @param: array routeName
     * @return :
     */
    public static function getMultiIsAccess($routeName)
    {
        $roleId = RoleConst::PublicRole;
        if (Auth::check()) {
            $roleId = Auth::user()->role_value;
        }
        $moduleInfor = CommonHelper::getModuleInforByMultiRouter($routeName);

        $access = array();
        foreach ($moduleInfor as $item) {
            if (self::isAccessByRole($roleId, $item['screenCode']) == true) {
                $access[$item['routeName']] = self::ENABLE_ROUTE;
            } else {
                $access[$item['routeName']] = self::DISABLE_ROUTE;
            }
        }
        return $access;
    }

    /**
     * @param $roleId
     * @param $screenCode
     * @return bool
     * validate has role
     */
    public static function isAccessByRole($roleId, $screenCode)
    {
        $configAcl = Config::get('acl');
        //Allow user has active access or system admin role
        if ((isset($configAcl[$roleId]) && isset($configAcl[$roleId][$screenCode]) && $configAcl[$roleId][$screenCode] == 1)
            || ($roleId == RoleConst::SysAdminRole)) {
            return true;
        }
        return false;
    }

    public static function getUserInfor()
    {
        $user = Auth::user();
        $userDetail = new \stdClass();
        $userDetail->id = 0;
        $userDetail->email = '';
        $userDetail->user_name = '';
        $userDetail->role_value = 0;
        $userDetail->avatar = '';
        $userDetail->name = '';
        $userDetail->gender = '';
        $userDetail->birth_date = now()->toDateTimeString();

        if (!empty($user)) {
            $userDetail = SDB::table('users')
                ->leftJoin('users_detail', 'users_detail.user_id', '=', 'users.id')
                ->where('users.id', $user->id)
                ->first();
        }

        return $userDetail;
    }

    public static function getUserInforById($userId=null)
    {
        $userDetail = new \stdClass();
        if ($userId!=null) {
            $userDetail = SDB::table('dtb_customer as users')
                            ->leftJoin("mtb_country as country","country.id","=","users.country_id")
                            ->leftJoin("plg_customerrank_dtb_customer as cus_rank","cus_rank.customer_id","=","users.customer_id")
                            ->leftJoin('sys_role_map_ec_role as role', 'cus_rank.customer_rank_id', '=', 'role.ec_role_value')
                            ->leftJoin("plg_customerrank_dtb_customer_rank as cus_rank_d",function($join){
                                $join->on("cus_rank_d.customer_rank_id","=","cus_rank.customer_rank_id");
                                $join->where("cus_rank_d.del_flg",SysConst::NOT_DEL_FLG);
                            })
                             ->where('users.customer_id', $userId)
                             ->selectRaw(
                                 'users.customer_id,users.status,users.sex, users.pref, users.company_name,users.job,
                                 users.country_id,users.name01,users.name02,
                                 users.kana01,users.kana02,users.zip01,users.zip02,users.zipcode,users.addr01,
                                 users.addr02,users.email,users.tel01,users.tel02,users.tel03,users.fax01,
                                 users.fax02,users.fax03,users.birth,users.first_buy_date,users.last_buy_date,users.buy_times,
                                 users.buy_total,
                                 role.role_value,
                                 cus_rank_d.name as rank_name,
                                 cus_rank_d.discount_rate,cus_rank_d.discount_value,cus_rank_d.free_delivfee,
                                 cus_rank_d.cond_amount,cus_rank_d.cond_buytimes,cus_rank_d.cond_point'
                             )
                             ->first();
        }
//        $userDetail->avatar = CommonHelper::getAvatar($userDetail->avatar);
        return $userDetail;
    }

    public static function createSalt($byte)
    {
        return bin2hex(random_bytes($byte));
    }

    public static function getUniqueSecretKey()
    {
        $unique   = Str::random(32);
        $customer = SDB::table('dtb_customer')->where('secret_key', $unique)->exists();
        if (!$customer) {
            return $unique;
        } else {
            return self::getUniqueSecretKey();
        }
    }

    public static function getUniqueResetKey()
    {
        $unique = Str::random(32);
        $customer = SDB::table('dtb_customer')->where('reset_key', $unique)->exists();
        if (!$customer) {
            return $unique;
        } else {
            return self::getUniqueSecretKey();
        }
    }

    public static function encodePassword($pass, $salt)
    {
        if (env('EC_AUTH_TYPE') == UserConst::SECRET_TYPE_PLAIN) {
            return $pass;
        }
        if ($salt == '') {
            $salt = env('EC_AUTH_MAGIC');
        }
        return hash_hmac(env('EC_PASSWORD_HASH_ALGOS'), $pass . ':' . env('EC_AUTH_MAGIC'), $salt);
    }

    public static function customLogin($request,$salt=null){
        $email = $request->email;
        $pass  = AuthHelper::encodePassword($request->input("password"), $salt);
        $user  = SDB::table("dtb_customer")
                    ->where("email",$email)
                    ->where("password",$pass)
                    ->where('del_flg',SysConst::NOT_DEL_FLG)
                    ->where('status',UserConst::active)
                    ->first();
        if(empty($user)){
            return false;
        }
        Auth::loginUsingId($user->customer_id);
        return true;
    }

    public static function getRoleValue(){
        $role_value = RoleConst::PublicRole;
        if(Auth::guard("api")->check()){
            $user = SDB::table("plg_customerrank_dtb_customer as ec_role")
                        ->leftJoin("sys_role_map_ec_role as role",function($join){
                            $join->on("role.ec_role_value","=","ec_role.customer_rank_id");
                            $join->where("role.del_flg",SysConst::NOT_DEL_FLG);
                        })
                        ->where("ec_role.customer_id",Auth::guard("api")->id())
                        ->selectRaw("ifnull(role.role_value,$role_value) as role_value")
                        ->first();
            if(!empty($user)){
                $role_value = $user->role_value;
            }else{
                $role_value = RoleConst::NormalUser;
            }
        }
        return $role_value;
    }

    public static function getRank(){
        $rank_id = UserConst::CUSTOMER_NO_RANK;
        if(Auth::guard("api")->check()){
            $rank = SDB::table("plg_customerrank_dtb_customer")
                       ->where("customer_id",Auth::guard("api")->id())
                       ->first();
            if(!empty($rank)){
                $rank_id = $rank->customer_rank_id;
            }
        }
        return $rank_id;
    }

    public static function joinProduct($query, $table)
    {
        return $query->join('dtb_product', function ($join) use ($table) {
            $join->on($table . '.product_id', 'dtb_product.product_id')
                ->where('dtb_product.status', 1)
                ->where('dtb_product.del_flg', UserConst::ENABLED)
                ->leftjoin('dtb_product_image', function ($join) {
                    $join->on('dtb_product.product_id', 'dtb_product_image.product_id');
                })
                ->leftjoin('dtb_product_class', function ($join) {
                    $join->on('dtb_product.product_id', 'dtb_product_class.product_id')
                        ->select('sale_limit', 'price01', 'price02');
                })
                ->leftjoin('dtb_product_tag', function ($join) {
                    $join->on('dtb_product.product_id', 'dtb_product_tag.product_id')
                        ->select('tag');
                });
        });
    }

}
