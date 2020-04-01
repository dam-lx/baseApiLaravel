<?php
/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 6/14/2018
 * Time: 10:28 AM
 */

namespace App\Acl\Services\Production;

use App\Core\Common\SDBStatusCode;
use App\Core\Dao\SDB;
use App\Core\Entities\DataResultCollection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use App\Acl\Services\Interfaces\AclServiceInterface;

class AclService extends BaseService implements AclServiceInterface
{

    public function getRoleInfoFromDB()
    {
        $result =  array();
        $roleList = SDB::table('sys_roles')
            ->selectRaw(
                'id AS role_id
                    ,	name AS role_name
                    ,	role_value AS role_value
                    ,	description AS role_description')
            ->get();
        $roleMapList = SDB::table('view_roles_map_action')->select()->get();
        $result[] = $roleList;
        $result[] = $roleMapList;
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
     * @param $name : name of config file. ex: 'acl' or 'app' or 'auth'....
     * @return mixed
     * HELPER: Read file config
     */
    public function getConfigDataFromFile($name)
    {
        $resultArray = Config::get($name);
        return $resultArray;
    }


    public function updateActiveAcl($roleMapId, $isActive)
    {
        $result=  new DataResultCollection();
        try{
            SDB::table('sys_role_map_screen')->whereRaw("id=? ",[$roleMapId])->update(array('is_active'=>$isActive));
            $this->generationAclFile();
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
            SDB::table('sys_role_map_screen')->update(array('is_active'=>$isActive));
            $this->generationAclFile();
            $result->status = SDBStatusCode::OK;
        }catch (\Exception $e){
            $result->status = SDBStatusCode::Excep;
            $result->message = $e->getMessage();
        }
        return $result;
    }

    public function getRoleList(){
        $roleList = SDB::table('sys_roles')->select()->get();
        return $roleList;
    }
    public function getModuleList(){
        $moduleList = SDB::table('sys_modules')->select()->get();
        return $moduleList;
    }
    /**
     * @return array
     */
    protected function getListScreen()
    {
        $controllers = [];
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
                $controllers[$i]['id'] = $id;
                $controllers[$i]['module'] = $_module;
                $controllers[$i]['controller'] = strtolower(end($_namespaces_chunks));
                $controllers[$i]['action'] = strtolower(end($_action));
                $controllers[$i]['screen_code']=$action['namespace']."\\".$controllers[$i]['controller']."\\".$controllers[$i]['action'];
                $controllers[$i]['description']=$action['namespace'];

            }
            $i++;
        }
        return ($controllers);
    }
    protected function getListModulesFromProjectStruct(){
        $moduleList = [];
        $i = 0;
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
        $moduleSkipAcl = ['dev'];
        SDB::table(('sys_modules'))->truncate();
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
        SDB::table('sys_modules')->insert($dataModule);
    }

    public function test()
    {
       echo 'acl.test';
    }
}

