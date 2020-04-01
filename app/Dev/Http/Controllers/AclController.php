<?php
/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 8/3/2018
 * Time: 10:59 AM
 */

namespace App\Dev\Http\Controllers;

use App\Core\Common\SDBStatusCode;
use App\Dev\Common\DevRoleConst;
use App\Dev\Helpers\CommonHelper;
use App\Dev\Helpers\ResponseHelper;
use App\Dev\Services\Interfaces\AclServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class AclController
{
    protected $service;
    public function __construct(AclServiceInterface $aclService)
    {
        $this->service = $aclService;
    }
    public function generationAclConfigFiles()
    {
        $this->service->generationAclFile();
    }


    public function importScreensList()
    {
        $this->service->initRoleDataToDB();
    }
    public function generationAclFile()
    {
        $this->service->generationAclFile();
        return null;
    }

    public function refreshAclDB(){
        $result = $this->service->generationRoleDataToDB();
        return ResponseHelper::JsonDataResult($result);
    }
    public function aclManangement(Request $request)
    {
        $role       = $request->role;
        $active     = 0;
        $module     = $request->module;
        $controller = $request->controller;
        $action     = $request->action;
        $dataAcl    = $this->service->getRoleInfo($request);
        foreach($dataAcl[1] as $items){
            ($items->is_active == DevRoleConst::active)?$active++:$active;
        }
        $checked = ($active == count($dataAcl[1]))?DevRoleConst::true:DevRoleConst::false;
        $roleList   = $this->service->getRoleList();
        $moduleList = $this->service->getModuleList();
        $limit      = ($request->limit!=null)?$request->limit:DevRoleConst::limit_20;
        return view("dev/acl", compact('dataAcl','roleList','moduleList','role','module','controller','action','limit','checked'));
    }

    public function updateAclActive(Request $request)
    {
        $active = $request->input('active');
        $roleMapId = $request->input('role_map_id');
        $isActive = 0;
        if (isset($active) && strtolower($active) == 'true') {
            $isActive = 1;
        }
        $this->service->updateActiveAcl($roleMapId, $isActive);
        return CommonHelper::convertVaidateErrorToCommonStruct(array());
    }

    /**
     * @return AclServiceInterface
     */
    public function updateAclFilter (Request $request)
    {
        $active   = $request->input('active');
        $isActive = 0;
        if (isset($active) && strtolower($active) == 'true') {
            $isActive = 1;
        }
        $result = $this->service->updateActiveFilterAcl($isActive,$request);
        return ResponseHelper::JsonDataResult($result);
    }

    public function updateAclActiveAll(Request $request){
        $ip   = $request->ip();
        $time = Carbon::now('Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s');
        $active = $request->input('active');
        $isActive = 0;
        if (isset($active) && strtolower($active) == 'true') {
            $isActive = 1;
        }
        $result = $this->service->updateActiveAclAll( $isActive);
        if($result->status == SDBStatusCode::OK){
            Log::info("IP: ($ip)-Time: ($time)");
        }
        return ResponseHelper::JsonDataResult($result);
    }

    public function userRole(){
        $dataUseRole = $this->service->getListUser();
        $roleList =  $this->service->getRoleList();

        return view("dev.userRole", compact('dataUseRole','roleList'));
    }

    public function updateUserRole(Request $request){
        $current_id = $request->input('$current_id');
        $current_role_value = $request->input('$current_role_value');
        $this->service->updateUserRole($current_id, $current_role_value);
        return CommonHelper::convertVaidateErrorToCommonStruct(array());
    }

}
