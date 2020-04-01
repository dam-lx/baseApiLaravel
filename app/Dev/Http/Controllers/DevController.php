<?php
/**
 * @author thanhnv
 */

namespace App\Dev\Http\Controllers;
use App\Core\Dao\SDB;
use App\Core\MailSender\UserRegisterSender;
use App\Dev\Entities\DataResultCollection;
use App\Dev\Helpers\ResponseHelper;
use App\Core\ExtendValidationRules\UpperCaseRule;
use App\Dev\Services\Interfaces\AclServiceInterface;
use App\Dev\Services\Interfaces\DevServiceInterface;
use App\Dev\Services\Interfaces\TranslateServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Core\Common\SDBStatusCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Validator;

class DevController extends Controller
{
    protected $devService;
    protected $aclService;
    protected $translateService;

    /**
     * Constructor
     */
    public function __construct(DevServiceInterface $devService, AclServiceInterface $aclService , TranslateServiceInterface $translateService)
    {
        $this->devService = $devService;
        $this->aclService = $aclService;
        $this->translateService = $translateService;
    }
    public function initProject()
    {
        $this->aclService->initRoleDataToDB();
        $this->aclService->generationAclFile();
    }

    /**
     * test readAcl file
     */
    public function readAclConfig()
    {
        $a = $this->devService->getConfigDataFromFile('acl');
        echo '<prev>';
        print_r($a);
    }

    public function index()
    {
        $readyImportTransFromRemote = true;
        return view("dev/index",compact('readyImportTransFromRemote'));
    }

    public function testCustomValidate(Request $request)
    {
        $validator =
            Validator::make($request->all(), [
                'text_code' => ['required|datetume',new UpperCaseRule()]
            ]);
        if ($validator->fails()) {
            dd($validator->errors());
        }
    }
    public function entityManagement(){
        $listSPCollection =  $this->devService->getAllSPList();
        $listSp = $listSPCollection->status==SDBStatusCode::OK?$listSPCollection->data:array();
        return view("dev/entitymanagement", compact('listSp'));
    }
    public function generateEntity()
    {
        $this->devService->generateEntityClass();
    }
    public function generateOneEntity(Request $request)
    {
        $spName = $request->input('name');
        $this->devService->generateSpecEntityClass($spName);
    }
    public function doc(){
        return view("dev/document");
    }
    public function importDataTranslationFromTest(){
        $result =  new DataResultCollection();
        try{
            $remoteConnection = SDB::connection('mysql_server_remote_translation');
            //get data remote
            $dataLangRemote = $remoteConnection->table('sys_languages')->get();
            $dataTransRemote = $remoteConnection->table('sys_translation')->get();
            $dataTransTypeRemote = $remoteConnection->table('sys_translate_type')->get();

            $dataLangInsert = json_decode(json_encode($dataLangRemote->toArray(),true),true);
            $dataTransInsert =  json_decode(json_encode($dataTransRemote->toArray(),true),true);
            $dataTransTypeInsert = json_decode(json_encode($dataTransTypeRemote->toArray(),true),true);
            SDB::beginTransaction();
            //truncate data translation in local
            SDB::table('sys_languages')->truncate();
            SDB::table('sys_translate_type')->truncate();
            SDB::table('sys_translation')->truncate();

            //Import data remove to local
            SDB::table('sys_languages')->insert($dataLangInsert);
            SDB::table('sys_translate_type')->insert($dataTransTypeInsert);
            SDB::table('sys_translation')->insert($dataTransInsert);
            SDB::commit();
            $result = $this->translateService->generationTranslateFileAndScript();
        }catch (\Exception $e){
            $result->status = SDBStatusCode::Excep;
            $result->message="controller:". $e->getMessage();
            SDB::rollBack();
        }
        return ResponseHelper::JsonDataResult($result);
    }
    public function log(){
        return view("dev/log");
    }
    public function runSchedules(){
    }
    public function test()
    {
        $data= SDB::select("select table_name As name from information_schema.tables WHERE TABLE_SCHEMA = '".env('DB_DATABASE')."'");
        /*Mail::to("nguyenthanhuet@gmail.com")->send(new UserRegisterSender("nguyenthanhuet@gmail.com"));
        echo 'test';*/
        dd($data);
    }
    
    public function data()
    {
        $array = [1,5,3,4,2];
        $arrayInsertion = [4,6,3,2,1,9,7];
        echo "<h3>Bubble sort:</h3>";
        for ($i=0;$i<count($array);$i++){
            echo $array[$i]."-";
        }
        echo "<br>";
        for($i=0;$i<(count($array)-1);$i++){
            $swap = false;
            for($j=0;$j<(count($array)-1-$i);$j++){
                echo "i:$i,j:$j-array[$j]=$array[$j]-array[".($j+1)."]=".$array[$j+1]."<br>";
                if($array[$j]>$array[$j+1]){
                    echo "Swape<br>";
                    $tam = $array[$j];
                    $array[$j] = $array[$j+1];
                    $array[$j+1] = $tam;
                    $swap = true;
                }
            }
            if(!$swap){
                break;
            }
        }
        echo "<h4>After sort:</h4>";
        for ($i=0;$i<count($array);$i++){
            echo $array[$i]."-";
        }
        echo "<h3>Insertion sort:</h3>";
        echo "<p>Array before</p>";
        for ($i=0;$i<count($arrayInsertion);$i++){
            echo $arrayInsertion[$i]."-";
        }
        echo "<p>Sorting</p>";
        for($i = 1; $i < count($arrayInsertion); $i++){
            echo  "i:".$i."<br>";
            // chon mot gia tri de chen
            $valueToInsert = $arrayInsertion[$i];
        
            // lua chon vi tri de chen
            $holePosition = $i;
        
            // kiem tra xem so lien truoc co lon hon gia tri duoc chen khong
            while ($holePosition > 0 && $arrayInsertion[$holePosition-1] > $valueToInsert){
                $arrayInsertion[$holePosition] = $arrayInsertion[$holePosition-1];
                $holePosition--;
                printf(" Di chuyen phan tu : %d\n" , $arrayInsertion[$holePosition]);
            }
        
            if($holePosition != $i){
                printf(" Chen phan tu : %d, tai vi tri : %d\n" , $valueToInsert,$holePosition);
                // chen phan tu tai vi tri chen
                $arrayInsertion[$holePosition] = $valueToInsert;
            }
        
            printf("Vong lap thu %d#:",$i);
            for ($j=0;$j<count($arrayInsertion);$j++){
                echo $arrayInsertion[$j]."-";
            }
        
        }
        $arraySelection = [4,6,3,2,1,9,7];
        echo "<h3>Selection sort:</h3>";
        echo "<p>Array before</p>";
        for ($i=0;$i<count($arraySelection);$i++){
            echo $arraySelection[$i]."-";
        }
        echo "<p>Sorting</p>";
        for($i=0;$i<count($arraySelection);$i++){
            $indexMin = $i;
            for($j=$i+1;$j<count($arraySelection);$j++){
                if($arraySelection[$j]<$arraySelection[$indexMin]){
                    $indexMin = $j;
                }
                if($indexMin!= $i){
                    $tam = $arraySelection[$indexMin];
                    $arraySelection[$indexMin] = $arraySelection[$i];
                    $arraySelection[$i] = $tam;
                }
            }
            printf("Vong lap thu %d#:",$i);
            for ($k=0;$k<count($arraySelection);$k++){
                echo $arraySelection[$k]."-";
            }
            echo "<br>";
        }
        $arrMerge = [];
    }
    
}

