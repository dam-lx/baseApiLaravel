<?php
/**
 * @author thanhnv
 */
namespace App\Acl\Http\Middleware;
use App\Acl\Http\Exceptions\AclException;
use App\Core\Helpers\AuthHelper;
use App\Core\Helpers\CommonHelper;
use App\Core\Common\SDBStatusCode;
use App\Core\Common\RoleConst;
use App\Core\Entities\DataResultCollection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Support\Facades\Log;

class Acl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $publicRole = RoleConst::PublicRole;
        $roleId = $publicRole;
        if(Auth::check()){
            $roleId = RoleConst::NormalUser;
        }
        $moduleInfor =  CommonHelper::getCurrentModuleInfor();
        if ($this->hasAcl($roleId,$moduleInfor->screenCode)==true ) {
            return $next($request);
        }else{
            throw new AclException();
        }

    }

    /**
     * @param $roleId
     * @param $screenCode
     * @return bool
     * validate has role
     */
    protected function hasAcl($roleId,$screenCode){
        $configAcl = Config::get('acl');
        //Allow user has active access or system admin role
        if((isset($configAcl[$roleId])&& isset($configAcl[$roleId][$screenCode]) && $configAcl[$roleId][$screenCode]==1)
            ||($roleId==RoleConst::SysAdminRole)){
            return true;
        }
        return false;
    }
}
