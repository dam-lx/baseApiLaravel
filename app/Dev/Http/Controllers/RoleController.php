<?php
/**
 * @author SonMT
 * Date: 8/23/2018
 *
 */

namespace App\Dev\Http\Controllers;

use App\Core\Common\SDBStatusCode;
use App\Core\Dao\SDB;
use App\Dev\Helpers\ResponseHelper;
// use App\Dev\Services\Interfaces\TranslateServiceInterface;
use App\Dev\Http\Requests\AddNewRoleRequest;
use App\Dev\Http\Requests\UpdateRoleRequest;
use App\Dev\Services\Interfaces\RoleServiceInterface;
use Validator;
use Illuminate\Http\Request;
use App\Dev\Entities\DataResultCollection;


class RoleController extends Controller
{
    protected $service;
    public function __construct(RoleServiceInterface $roleService)
    {
        $this->service = $roleService;
    }

    public function roleManagement()
	{
		$roles = $this->service->getRoleList()->data;
		return view('dev.roles', compact('roles'));
	}
	public function getCreateNewRoleItem()
	{
		return view('dev/addrole');
	}

	public function createNewRoleItem(AddNewRoleRequest $request)
	{
        $dataResult = $this->service->addNewRole($request);
        return ResponseHelper::JsonDataResult($dataResult);
	}

	public function getEditRoleItem(Request $request)
	{
		$id= $request->id;
		$edit = SDB::table('sys_roles')->where("id",$id)->get();
		$edit = $edit[0];
		return view('dev/editrole', compact('edit'));
	}

	public function updateRole(UpdateRoleRequest $request)
	{
        $result = $this->service->updateRole($request);
		return ResponseHelper::JsonDataResult($result);
	}

	public function deleteRole(Request $request)
	{
		$id = $request->id;
        $result = $this->service->deleteRole($id);
        return ResponseHelper::JsonDataResult($result);
	}


}
