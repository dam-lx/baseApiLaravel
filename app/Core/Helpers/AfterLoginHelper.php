<?php

namespace App\Core\Helpers;

/**
 * Created by PhpStorm.
 * User: my computer
 * Date: 6/30/2018
 * Time: 2:05 AM
 */

use App\Core\Common\RoleConst;
use Illuminate\Support\Facades\Auth;

class AfterLoginHelper
{
    public static function redirectInitPage($role){
        switch ($role){
            case RoleConst::SysAdminRole:{
                $redirectTo = route('backend_dashboard');
                break;
            }
            case  RoleConst::Manager:{
                $redirectTo = route('backend_dashboard');
                break;
            }
            case  RoleConst::NormalUser:{
                Auth::logout();
                $redirectTo = route('backend.login');
                break;
            }
            default:
                $redirectTo = '';
        }
        return $redirectTo;
    }
}
