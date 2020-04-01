<?php
/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 6/14/2018
 * Time: 10:28 AM
 */

namespace App\Dev\Services\Production;

use App\Core\Dao\SDB;
use App\Dev\Dao\DEVDB;
use Illuminate\Support\Facades\Config;
use App\Core\Common\SDBStatusCode;
use App\Core\Common\CoreConst;
use App\Dev\Services\Interfaces\DevServiceInterface;
use App\Dev\Entities\DataResultCollection;
use Mockery\CountValidator\Exception;
use App\Dev\Helpers\CommonHelper;

class DevService extends BaseService implements DevServiceInterface
{
    public function getLanguageCodeList():DataResultCollection
    {
        $result= new DataResultCollection();
        $result->data = DEVDB::table('sys_languages')->select()->get();
        $result->status =  SDBStatusCode::OK;
        return $result;
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

    public function generateEntityClass(){
        try{
            //Generate Storeprocedure entity
            $spsList =  $this->getAllSPSList();
            $modules =  DEVDB::select('SELECT module_code FROM sys_modules');
            if($spsList->status==SDBStatusCode::OK){
                foreach ($spsList->data as $row){
                    DEVDB::generateEntityClass($row->Name,$this->getModuleNameFromSpName($row->Name,$modules));
                }
            }
            //Generate Table and View entity
            $tableList =  $this->getAllTableList();
            if($tableList->status==SDBStatusCode::OK){
                foreach ($tableList->data as $row){
                    DEVDB::generateEntityClassByTable($row->name);
                }
            }
        }catch (Exception $e){
            CommonHelper::CommonLog($e->getMessage());
        }
    }
    public function generateSpecEntityClass($spName){
        $modules =  DEVDB::select('SELECT module_code FROM sys_modules');
        DEVDB::generateEntityClass($spName,$this->getModuleNameFromSpName($spName,$modules));
    }
    public function getAllSPList():DataResultCollection{
        $spsList =  $this->getAllSPSList();
        return $spsList;
    }
    /**
     * @param $procedureName
     * @param $modules
     * @return string
     */
    protected function getModuleNameFromSpName($procedureName,$modules){
        $result = CoreConst::CoreModuleName ;//default
        $delimiter = '_';
        $procedureName =  strtolower($procedureName);
        $listModule = array();
        if(!empty($modules)){
            foreach ($modules as $item){
                $listModule[] = $item->module_code;
            }
        }
        if(strpos($procedureName, $delimiter) !== false){
            $module =explode ($delimiter,$procedureName)[0];
            if(in_array($module,$listModule)){
                $result =  ucfirst($module);
            }
        }
        return $result;
    }
    protected function getAllSPSList():DataResultCollection{
        $result =  new DataResultCollection();
        try{
            $data= DEVDB::select("SHOW PROCEDURE STATUS WHERE Db = '".env('DB_DATABASE')."' AND Type = 'PROCEDURE'");
            $result->status =  SDBStatusCode::OK;
            $result->data = $data;
        }catch (\Exception $e){
            $result->status =  SDBStatusCode::Excep;
            $result->message = $e->getMessage();
        }
        return $result;
    }
    protected function getAllTableList():DataResultCollection{
        $result =  new DataResultCollection();
        try{
            $data= DEVDB::select("select table_name As name from information_schema.tables WHERE TABLE_SCHEMA = '".env('DB_DATABASE')."'");
            $result->status =  SDBStatusCode::OK;
            $result->data = $data;
        }catch (\Exception $e){
            $result->status =  SDBStatusCode::Excep;
            $result->message = $e->getMessage();
        }
        return $result;
    }

    public function test()
    {
       echo 'dev.test';
    }
}

