<?php
/**
 * @author thanhnv
 */

namespace App\Acl\Http\Controllers;
use App\Acl\Services\Interfaces\AclServiceInterface;
use App\Dev\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Core\Helpers\CommonHelper;
use Validator;

class AclController extends Controller
{
    protected $service;

    /**
     * Constructor
     */
    public function __construct(AclServiceInterface $aclService)
    {
        $this->service = $aclService;
    }

    public function index()
    {
        $dataAcl = $this->service->getRoleInfoFromDB();
        $roleList =  $this->service->getRoleList();
        $moduleList =$this->service->getModuleList();
        return view("acl/index", compact(['dataAcl','roleList','moduleList']));
    }

    public function updateAclActive(Request $request)
    {
        $active = $request->input('active');
        $roleMapId = $request->input('role_map_id');
        $isActive = 0;
        if (isset($active) && strtolower($active) == 'true') {
            $isActive = 1;
        }
        $result = $this->service->updateActiveAcl($roleMapId, $isActive);
        return ResponseHelper::JsonDataResult($result);
    }
    public function updateAclActiveAll(Request $request){
        $active = $request->input('active');
        $isActive = 0;
        if (isset($active) && strtolower($active) == 'true') {
            $isActive = 1;
        }
        $result= $this->service->updateActiveAclAll( $isActive);
        return ResponseHelper::JsonDataResult($result);
    }
    public function userAcl(){
        return view("acl/useracl");
    }
    public function test()
    {
        $this->service->test();
    }

}
