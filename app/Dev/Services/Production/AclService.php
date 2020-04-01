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
use App\Dev\Common\DevRoleConst;
use App\Dev\Dao\DEVDB;
use App\Dev\Entities\DataResultCollection;
use App\Dev\Services\Interfaces\AclServiceInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

class AclService extends BaseService implements AclServiceInterface
{
    /**
     * innitization role in database
     * @return bool
     *
     */
    public function initRoleDataToDB(){
        $data = $this->getListScreen();
        $systemAdminRole = RoleConst::SysAdminRole;
        $roleList = $this->getRoleList();
        //Insert sys screen data
        DEVDB::table('sys_screens')->truncate();
        DEVDB::table('sys_screens')->insert($data);

        //Insert dev module data
        $this->importModuleListToDB();

        //Mapping role with screen
        DEVDB::table('sys_role_map_screen')->truncate();
        $id = 0;
        $dataRolesMapping = array();
        if(!empty($roleList)){
            foreach ($roleList as $role) {
                if(!empty($data)){
                    foreach ($data as $item) {
                        $id++;
                        $isActive = 0;
                        if ($role->role_value == $systemAdminRole) {
                            $isActive = 1;
                        }
                        $dataRolesMapping[] = array(
                            'id' => $id,
                            'role_value' => $role->role_value,
                            'screen_id' => $item['id'],
                            'is_active' => $isActive
                        );
                    }
                }

            }

        }

        DEVDB::table('sys_role_map_screen')->insert($dataRolesMapping);
        return true;
    }

    public function getRoleList(){
        $roleList = SDB::table('sys_roles')->select()->get();
        return $roleList;
    }
    public function getModuleList(){
        $moduleList = SDB::table('sys_modules')->select()->get();
        return $moduleList;
    }

    public function updateActiveAcl($roleMapId, $isActive)
    {
        $result=  new DataResultCollection();
        try{
            DEVDB::table('sys_role_map_screen')->whereRaw("id=? ",[$roleMapId])->update(array('is_active'=>$isActive));
            $result->status = SDBStatusCode::OK;
        }catch (\Exception $e){
            $result->status = SDBStatusCode::Excep;
            $result->message = $e->getMessage();
        }
        return $result;

    }

    public function updateActiveAclAll($isActive)
    {
        $result=  new DataResultCollection();
        try{
            DEVDB::table('sys_role_map_screen')->update(array('is_active'=>$isActive));
            $result->message = "Update active screen map success!";
            $result->status = SDBStatusCode::OK;
        }catch (\Exception $e){
            $result->status = SDBStatusCode::Excep;
            $result->message = $e->getMessage();
        }
        return $result;
    }

    public function updateActiveFilterAcl($isActive,$request)
    {
        $result=  new DataResultCollection();
        try{
             DEVDB::table('sys_role_map_screen')
                    ->when($request->role != null, function ($query) use ($request) {
                        return $query->where("role_value",(int) $request->role);
                    })
                    ->whereIn("id",$request->arrScreenId)
                    ->update(array('is_active'=>$isActive));
            $result->status = SDBStatusCode::OK;
            $result->message = "Change active success!";
        }catch (\Exception $e){
            $result->status = SDBStatusCode::Excep;
            $result->message = $e->getMessage();
        }
        return $result;
    }
    /**
     * @return array
     * HELPER: get role mapping screen to Array
     */
    public function getRoleMapArray()
    {
        $resultArr = [];
        $roleInfo = $this->getRoleInfoFromDB();
        if (!empty($roleInfo)) {
            $roles = $roleInfo[0];
            $roleMap = $roleInfo[1];
            if (!empty($roles)) {
                foreach ($roles as $item) {
                    $resultArr[$item->role_value]['data'] = array();
                    $resultArr[$item->role_value]['name'] = $item->role_name;
                }
                if (!empty($resultArr)) {
                    foreach ($resultArr as $itemKey => $itemValue) {
                        if (!empty($roleMap)) {
                            foreach ($roleMap as $roleMapItem) {
                                if ($itemKey == $roleMapItem->role_value) {
                                    $resultArr[$itemKey]['data'][$roleMapItem->screen_code] = $roleMapItem->is_active;
                                }
                            }
                        }
                    }

                }
            }
        }

        return $resultArr;
    }

    /**
     * @param $roleMapScreen : array role map
     * Struct input as:
     * HELPER: generate acl file to config folder
     */
    public function generationAclFile()
    {
        $roleMapScreen = $this->getRoleMapArray();
        $fileName = 'acl';//fixed, warning: Must not dupplicate with other config file, which existed.
        $fileAcl = base_path() . '/config/' . $fileName . '.php';

        //Create file validate if not existed
        if (file_exists($fileAcl)) {
            $fh = fopen($fileAcl, 'w');
        } else {
            $fh = fopen($fileAcl, 'w');
        }
        $contentFile = "<?php \n";
        $contentFile .= "//This is dev automatic generate \n ";
        //Generate content file

        $contentFile .= "return [\n";
        if (!empty($roleMapScreen)) {
            foreach ($roleMapScreen as $roleValue => $value) {
                $contentFile .= "\t'" . $roleValue . "'=>[ //".$value['name']." \n";
                if (isset($value) &&!empty($value['data'])) {
                    foreach ($value['data'] as $screenCode => $isActive) {
                        $contentFile .= "\t\t'" . $screenCode . "'=>'" . $isActive . "',\n";
                    }
                }
                $contentFile .= "\t],\n";
            }
        }
        $contentFile .= '];';

        //Write content file
        fwrite($fh, $contentFile);
        fclose($fh);

    }
    /**
     * @return array
     * generation screens list, insert role map screen and merger role to database.
     */
    public function generationRoleDataToDB():DataResultCollection
    {
        $result = new DataResultCollection();
        //Insert dev module data
        try{
            DEVDB::beginTransaction();
            $this->importModuleListToDB();
            $data = $this->getListScreen();
            DEVDB::execSPs('DEBUG_IMPORT_AND_MERGER_ROLE_ACT',array(json_encode($data,JSON_FORCE_OBJECT )));
            $result->status =  SDBStatusCode::OK;
            DEVDB::commit();
        }catch (\Exception $e){
            DEVDB::rollBack();
            $result->status =  SDBStatusCode::Excep;
            $result->message = $e->getMessage();
        }
        return $result;
    }

    public function getRoleInfoFromDB()
    {
        $result =  array();
        $roleList = DEVDB::table('sys_roles')
                         ->selectRaw(
                                 'id AS role_id
                    ,	name AS role_name
                    ,	role_value AS role_value
                    ,	description AS role_description')
                         ->get();
        $roleMapList = DEVDB::table('view_roles_map_action')->select()->get();
        $result[] = $roleList;
        $result[] = $roleMapList;
        return $result;
    }

    public function getRoleInfo($request)
    {
        $result     = array();
        $role       = $request->role;
        $module     = $request->module;
        $controller = $request->controller;
        $action     = $request->action;
        $limit      = ($request->limit!=null)?$request->limit:DevRoleConst::limit_20;
        try {
            $roleList = DEVDB::table('sys_roles')
                             ->selectRaw(
                                     'id AS role_id
                    ,	name AS role_name
                    ,	role_value AS role_value
                    ,	description AS role_description')
                             ->get();
            $roleMapList = DEVDB::table('view_roles_map_action')
                                ->when($role != null, function ($query) use ($request, $role) {
                                    return $query->where("role_name", "like", "%" . $role . "%");
                                })
                                ->when($module != null, function ($query) use ($request, $module) {
                                    return $query->where("module", "like", "%" . $module . "%");
                                })
                                ->when($controller != null, function ($query) use ($request, $controller) {
                                    return $query->where("controller", "like", "%" . $controller . "%");
                                })
                                ->when($action != null, function ($query) use ($request, $action) {
                                    return $query->where("action", "like", "%" . $action . "%");
                                })
                                ->select()
                                ->paginate($limit);
        } catch (\Exception $exception) {
            \Log::debug($exception->getMessage());
        }
        $result[] = $roleList;
        $result[] = $roleMapList;
        return $result;
    }
    /**
     * @return array
     * get all screen informations
     */
    protected function getListScreen()
    {
        $screens = [];
        $i = 0;
        $id = 0;
        $listRouter = Route:: getRoutes()->getRoutes();
        foreach ($listRouter as $route) {
            $action = $route->getAction();
            if (array_key_exists('controller', $action)) {
                $_module = strtolower(trim(str_replace('App\\', '', $action['namespace']), '\\'));
                $_module =  explode("\\",$_module)[0];
                $id++;
                $_action = explode('@', $action['controller']);

                $_namespaces_chunks = explode('\\', $_action[0]);

                $screens["$i"]['id'] = $id;
                $screens["$i"]['module'] = $_module;
                $screens["$i"]['controller'] = strtolower(end($_namespaces_chunks));
                $screens["$i"]['action'] = strtolower(end($_action));
                $screens["$i"]['screen_code']=$action['namespace']."\\".$screens[$i]['controller']."\\".$screens[$i]['action'];
                $screens["$i"]['description']=$action['namespace'];
            }
            $i++;
        }
        $screens=  array_unique($screens,SORT_REGULAR);
        return ($screens);
    }

    protected function getListModulesFromProjectStruct(){
        $moduleList = [];
        $i = 0;
        $id = 0;
        $listRouter = Route:: getRoutes()->getRoutes();
        foreach ($listRouter as $route) {
            $action = $route->getAction();
            if (array_key_exists('controller', $action)) {
                $_module = strtolower(trim(str_replace('App\\', '', $action['namespace']), '\\'));
                $_module =  explode("\\",$_module)[0];
                $moduleList[]= $_module;
            }
            $i++;
        }
        return (array_unique($moduleList));
    }

    /**
     * read project struct to generation module list to Database
     */
    protected function importModuleListToDB(){
        $moduleSkipAcl =Config::get('app.SKIPS_ACL_MODULE');
        DEVDB::table(('sys_modules'))->truncate();
        $dataModule = [];
        $dataModuleList =  $this->getListModulesFromProjectStruct();
        if(!empty($dataModuleList)){
            $i = 0;
            foreach ($dataModuleList as $itemScreen){
                $i++;
                $dataModule[] = array(
                    'id'=>$i,
                    'module_code'=>$itemScreen,
                    'module_name'=>$itemScreen,
                    'order_value'=>$i,
                    'is_skip_acl'=>(in_array($itemScreen,$moduleSkipAcl)?1:0),
                );
            }
        }
        DEVDB::table('sys_modules')->insert($dataModule);
    }

    public function getListUser(){
        return DEVDB::table('view_debug_user_role_list_users')->select()->get();
    }
    public function updateUserRole($current_id, $current_role_value)
    {
        DEVDB::table('users')->whereRaw('id=?',[$current_id])->update(array('role_value'=>$current_role_value));
    }



}
