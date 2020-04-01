<?php
/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 8/3/2018
 * Time: 10:44 AM
 */

namespace App\Dev\Services\Production;

use App\Core\Common\SDBStatusCode;
use App\Core\Dao\SDB;
use App\Dev\Entities\DataResultCollection;
use App\Dev\Http\Requests\AddNewRoleRequest;
use App\Dev\Http\Requests\UpdateRoleRequest;
use App\Dev\Services\Interfaces\RoleServiceInterface;

class RoleService extends BaseService implements RoleServiceInterface
{
    public function addNewRole(AddNewRoleRequest $request):DataResultCollection
    {
        $result = new DataResultCollection();
        try {
            SDB::beginTransaction();
            $newRoleValue = 1;
            if ($request->input('role_value') == null) {
                $max_role_value = SDB::table('sys_roles')->max('role_value');
                $newRoleValue = $max_role_value + 1;
                $request->merge(['role_value' => $newRoleValue]);
            }
            SDB::table('sys_roles')->insert($request->all());
            $screenList = SDB::table('sys_screens')->select('id')->get();
            $dataRoleMap = array();
            if (!empty($screenList)) {
                foreach ($screenList as $item) {
                    $dataRoleMap[] = array(
                        'role_value' => $newRoleValue,
                        'screen_id' => $item->id,
                        'is_active' => 0
                    );
                }
            }
            SDB::table('sys_role_map_screen')->insert($dataRoleMap);
            SDB::commit();
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $e) {
            SDB::rollBack();
            $result->status = SDBStatusCode::Excep;
            $result->message = $e->getMessage();
        }
        return $result;
    }
    public function getRoleList():DataResultCollection{
        $result = new DataResultCollection();
        try {
            $roles = SDB::table('sys_roles')->get();
            $result->data = $roles;
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $e) {
            $result->status = SDBStatusCode::Excep;
            $result->message = $e->getMessage();
        }
        return $result;
    }
    public function updateRole(UpdateRoleRequest $request):DataResultCollection{
        $result = new DataResultCollection();
        try {
            $id = $request->id;
            SDB::table('sys_roles')->where("id",$id)->update([
                "name" => $request->name,
                "role_value" => $request->role_value,
                "description" => $request->description
            ]);
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $e) {
            $result->status = SDBStatusCode::Excep;
            $result->message = $e->getMessage();
        }
        return $result;
    }
    public function deleteRole($id):DataResultCollection{
        $result = new DataResultCollection();
        try {
            SDB::beginTransaction();
            $roleInfor = SDB::table('sys_roles')->where("id",$id)->select('role_value')->first();
            if(!empty($roleInfor)){
                $roleValue =  $roleInfor->role_value;
                SDB::table('sys_roles')->where("id",$id)->delete();
                SDB::table('sys_role_map_screen')->where('role_value','=',$roleValue)->delete();
            }
            SDB::commit();
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $e) {
            SDB::rollBack();
            $result->status = SDBStatusCode::Excep;
            $result->message = $e->getMessage();
        }
        return $result;
    }
}
