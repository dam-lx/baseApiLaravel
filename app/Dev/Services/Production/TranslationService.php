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
use App\Dev\Helpers\CommonHelper;
use App\Dev\Dao\DEVDB;
use App\Dev\Entities\DataResultCollection;
use App\Dev\Services\Interfaces\TranslateServiceInterface;
use Illuminate\Support\Facades\Lang;

class TranslationService extends BaseService implements TranslateServiceInterface
{
    /**
     * @param $validateArray
     * @param $fileName
     * HELPER: Generation Config File contain text translated.
     */
    public function generationTranslateFile($translateType, $fileName)
    {
        $validateArray = $this->getTranslateMessageArray($translateType);
        $folderLangPath = base_path() . '/resources/lang/';
        foreach ($validateArray as $langCode => $langGroupContent) {
            $langFolder = strtolower($folderLangPath . $langCode);
            if (!is_dir($langFolder)) {
                mkdir($langFolder);
            }
            $fileTranslate = $langFolder . '/' . $fileName . '.php';

            //Create file validate if not existed
            $fh = fopen($fileTranslate, 'w');
            $contentFile = "<?php \n";
            $contentFile .= "//This is dev automatic generate \n ";
            $contentFile = $contentFile . 'return ' . var_export($langGroupContent, true) . ';';
            //Write content file
            fwrite($fh, $contentFile);
            fclose($fh);
        }
    }

    /**
     * @param $validateArray
     * @param $fileName
     * HELPER: Generation Javascript File contain text translated.
     */
    public function generationTranslateScript($fileName = "text")
    {
        $transTypeList =  $this->getTransType();
        $finalData = array();
        if(!empty($transTypeList)){
            foreach ($transTypeList as $transType){
                $validateArray = $this->getTranslateMessageArray($transType->code);
                foreach ($validateArray as $key=>$value){
                    $finalData[$key][$transType->code] =$value;
                }
            }
        }

        $dir = base_path() . '/public/js/lang/';
        $folderLangPath = $dir . $fileName . '.js';
        //Create file validate if not existed
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $fh = fopen($folderLangPath, 'w');
        $contentFile = "//This is dev automatic generate \n ";
        $contentFile .= "var _messageTranslation = \n";
        $txt = json_encode($finalData,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $contentFile .= $txt . ';';
        //Write content file
        fwrite($fh, $contentFile);
        fclose($fh);

    }

    public function generationTranslateFileAndScript():DataResultCollection
    {
        $result =  new DataResultCollection();
        try{
            $transTypeList = DEVDB::table('sys_translate_type')->select(['id','code','comment'])->get();
            if (!empty($transTypeList)) {
                foreach ($transTypeList as $item) {
                    $this->generationTranslateFile($item->code, $item->code);
                }
            }
            $this->generationTranslateScript();
            $result->status =  SDBStatusCode::OK;
        }catch (\Exception $e){
            $result->status =  SDBStatusCode::Excep;
            $result->message = $e->getMessage();
        }
        return $result;
    }

    public function getNewTransComboList()
    {
        return DEVDB::table('sys_translate_type')->select(['id','code','comment'])->get();
    }

    /**
     * @return array
     * generation screens list, insert to database and innitization system administrator role.
     */
    public function generationTransDataToDB():DataResultCollection
    {
        $result =  new DataResultCollection();
        try{
            $dir = base_path() . '/resources/lang';
            $langList = array_diff(scandir($dir), array('..', '.'));
            $id = 0;
            DEVDB::table('sys_translation')->truncate();
            if (!empty($langList)) {
                foreach ($langList as $lang) {
                    $dir = base_path() . '/resources/lang/' . $lang;
                    $typeTranslateList = array_diff(scandir($dir), array('..', '.'));
                    Lang::setLocale($lang);
                    if (!empty($typeTranslateList)) {
                        $dataTrans = array();
                        foreach ($typeTranslateList as $translateFileName) {
                            $typeTranslate = str_replace('.php', '', $translateFileName);
                            $tran = Lang::get($typeTranslate);
                            $dataTrans[] = array(
                                'id' => $id,
                                'lang_code' => strtolower($lang),
                                'text' => json_encode($tran, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                                'translate_type' => $typeTranslate,
                                'created_at' => now(),
                                'is_deleted' => 0
                            );

                        }
                        if (!empty($dataTrans)) {
                            DEVDB::table('sys_translation')->insert($dataTrans);
                        }
                    }
                }
            }
            $result->status =  SDBStatusCode::OK;
        }catch (\Exception $e){
            $result->status =  SDBStatusCode::Excep;
            $result->message =  $e->getMessage();
        }
        return $result;

    }

    /**
     * @param $code (struct multip level : filename.code_level1.code_level2....)
     * @return DataResultCollection
     * delete inside all of language
     */
    public function deleteTranslate( $code)
    {
        $result = new DataResultCollection();
        try {
            $keyInfor = explode('.', $code);
            $transType = $keyInfor[0];
            $transTextCode = $keyInfor[1];
            unset($keyInfor[0]);//remove translation type
            $lastKey = array_pop($keyInfor);
            $keyInsideTextValue = $keyInfor;
            $newText = "";
            $transEdit = DEVDB::table('sys_translation')->whereRaw("translate_type = ? ", [$transType])->select()->get()->toArray();
            foreach ($transEdit as $transEditItem){
                $lang = $transEditItem->lang_code;
                $id = $transEditItem->id;
                $text = $transEditItem->text;
                if (CommonHelper::isJSON($text)) {
                    $textArr = json_decode($text, true);
                    $tmpArr = &$textArr;
                    foreach ($keyInsideTextValue as $key) {
                        $tmpArr = &$tmpArr[$key];
                    }
                    unset($tmpArr[$lastKey]);
                    $textArr = CommonHelper::array_non_empty_items($textArr);
                    if(!empty($textArr)){
                        $newText = json_encode($textArr, true);
                        SDB::table('sys_translation')->whereRaw("id = ?", [$id])
                            ->update(
                                array(
                                    'text'=>$newText,
                                    'updated_at'=>now())
                            );
                    }else{
                        DEVDB::table('sys_translation')->whereRaw("lang_code = ? AND translate_type = ?  ", [$lang, $transType])->delete();
                    }

                } else {
                    DEVDB::table('sys_translation')->whereRaw("lang_code = ? AND translate_type = ? ", [$lang, $transType])->delete();
                }
            }
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $e) {
            $result->status = SDBStatusCode::Excep;
            $result->message = $e->getMessage();
        }
        return $result;
    }

    /**
     * @param $lang : language code ( jp, en, fr....)
     * @param $code
     * @param $transText
     * @return DataResultCollection
     */
    public function updateTranslateText($lang, $code, $transText)
    {
        $result = new DataResultCollection();
        try {
            $keyInfor = explode('.', $code);
            $transType = $keyInfor[0];
            unset($keyInfor[0]);//remove translation type
            $keyInsideTextValue = $keyInfor;
            $newText = "";
            $transEdit = SDB::table('sys_translation')->whereRaw("lang_code = ? AND translate_type = ? ", [$lang, $transType])->first();
            if(empty($transEdit)){
                $valueArr =  array();
                $tmpArr = &$valueArr;
                foreach ($keyInfor as $codeItem){
                    $tmpArr = &$tmpArr[$codeItem];
                }
                $tmpArr = $transText;
                $data = array(
                    'lang_code'=>$lang,
                    'translate_type'=>$transType,
                    'text'=> json_encode (
                        $valueArr,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                    ),
                    'created_at'=>now()
                );
                SDB::table('sys_translation')->insert($data);

            }else{
                $id = $transEdit->id;
                $text = $transEdit->text;
                if (CommonHelper::isJSON($text)) {
                    $textArr = json_decode($text, true);
                    $tmpArr = &$textArr;
                    foreach ($keyInsideTextValue as $key) {
                        $tmpArr = &$tmpArr[$key];
                    }
                    $tmpArr = $transText;
                    $newText = json_encode($textArr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                } else {
                    $newText = $transText;
                }
                SDB::table('sys_translation')->whereRaw("id = ?", [$id])
                    ->update(
                        array(
                            'text'=>$newText,
                            'updated_at'=>now())
                    );
            }

            $result->status = SDBStatusCode::OK;
        } catch (\Exception $e) {
            $result->status = SDBStatusCode::Excep;
            $result->message = $e->getMessage();
        }
        return $result;
    }

    /**
     * @param $transType : validation, label....( file name of lang)
     * @param $transTextCode : keycode as "validation.password.error..." .... multip level code
     * @param $textTrans : value text
     * @return DataResultCollection
     */
    public function insertTranslationItem($transType, $transTextCode, $textTrans)
    {
        $result =  new DataResultCollection();
        $transTextCode = $transType.'.'.$transTextCode;
        $text =  json_decode($textTrans,true);
        foreach ($text as $key=>$value){
            try {
                $keyInfor = explode('.', $transTextCode);
                $transType = $keyInfor[0];
                unset($keyInfor[0]);//remove translation type
                $keyInsideTextValue = $keyInfor;
                $newText = "";
                $transEdit = SDB::table('sys_translation')->whereRaw("lang_code = ? AND translate_type = ? ", [$key, $transType])->first();
                if(!empty($transEdit)){
                    $id = $transEdit->id;
                    $text = $transEdit->text;
                    if (CommonHelper::isJSON($text)) {
                        $textArr = json_decode($text, true);
                        $tmpArr = &$textArr;
                        foreach ($keyInsideTextValue as $key) {
                            $tmpArr = &$tmpArr[$key];
                        }
                        if($tmpArr!=null && $tmpArr !=''){
                            $result->status = SDBStatusCode::WebError;
                            $error = array("text_code"=>"trans_exists_code");
                            $result->data = $error;
                            $newText = json_encode($textArr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }else{
                            $tmpArr = $value;
                            $newText = json_encode($textArr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        }
                    } else {
                        $newText = $value;
                    }
                    DEVDB::table('sys_translation')->whereRaw("id = ? ",[$id])->update(
                        array(
                            'text'=>$newText,
                            'updated_at' => now()
                        )
                    );
                }else{
                    $valueArr =  array();
                    $tmpArr = &$valueArr;
                    foreach ($keyInfor as $codeItem){
                        $tmpArr = &$tmpArr[$codeItem];
                    }
                    $tmpArr = $value;
                    $data = array(
                        'lang_code'=>$key,
                        'translate_type'=>$transType,
                        'text'=> json_encode (
                            $valueArr,true
                        ),
                        'created_at'=>now()
                    );
                    SDB::table('sys_translation')->insert($data);
                }
                if($result->status != SDBStatusCode::WebError){
                    $result->status = SDBStatusCode::OK;
                }
            } catch (\Exception $e) {
                $result->status = SDBStatusCode::Excep;
                $result->message = $e->getMessage();
            }

        }
        return $result;
    }

    public function getLanguageCodeList(): DataResultCollection
    {
        $result =  new DataResultCollection();
        $result->data = DEVDB::table('sys_languages')->select()->get();
        $result->status = SDBStatusCode::OK;
        return $result;
    }

    public function getTranslateList(): DataResultCollection
    {
        $result = new DataResultCollection();
        $resuiltArr = [];
        $inforArr = [];
        $lang = DEVDB::table('sys_languages')->select()->get();
        if (!empty($lang)) {

            foreach ($lang as $item) {
                $resuiltArr[$item->code] = array();
            }
            $dataTrans = DEVDB::table('view_get_translation_data')->select()->get();
            if (!empty($resuiltArr)) {
                foreach ($resuiltArr as $itemKey => $itemValue) {
                    if (!empty($dataTrans)) {
                        foreach ($dataTrans as $ruleItem) {
                            if ($itemKey == $ruleItem->lang_code) {
                                if (CommonHelper::isJSON($ruleItem->text)) {
                                    $value = json_decode($ruleItem->text, true);
                                } else {
                                    $value = $ruleItem->text;
                                }
                                $resuiltArr[$itemKey][$ruleItem->translate_type_code] = $value;
                                $inforArr[$itemKey][$ruleItem->translate_type_code] = array(
                                    'lang_code' => $ruleItem->lang_code,
                                    'lang_type' => $ruleItem->translate_type_code,
                                    'value' => $value
                                );

                            }
                        }
                    }
                }

            }
        }

        $result->data['value_arr'] = $resuiltArr;
        $result->data['infor_arr'] = $inforArr;
        $result->status = SDBStatusCode::OK;
        return $result;
    }

    /**
     * @param $translateType
     * @return array
     * HELPER: Get data translated text by type ( validation, auth, label...)
     */
    public function getTranslateMessageArray($translateType = '')
    {
        $resuiltArr = [];
        $lang = DEVDB::table('sys_languages')->select()->get();
        if (!empty($lang)) {

            foreach ($lang as $item) {
                $resuiltArr[$item->code] = array();
            }
            $transData = $this->getTranslateDataList($translateType, '');

            if (!empty($resuiltArr)) {
                foreach ($resuiltArr as $itemKey => $itemValue) {
                    if ($transData->status == SDBStatusCode::OK) {
                        foreach ($transData->data as $transItem) {
                            if ($itemKey == $transItem->lang_code) {
                                if (CommonHelper::isJSON($transItem->text)) {
                                    $resuiltArr[$itemKey] = json_decode($transItem->text, true);
                                } else {
                                    $resuiltArr[$itemKey] = $transItem->text;
                                }

                            }
                        }
                    }
                }

            }
        }
        return $resuiltArr;
    }

    public function initTranslateType():DataResultCollection
    {
        $resullt =  new DataResultCollection();
        try{
            $dir = base_path() . '/resources/lang';
            $langList = array_diff(scandir($dir), array('..', '.'));
            $typeTranslateList =  array();
            foreach ($langList as $lang){
                $dir = base_path() . '/resources/lang/' . $lang;
                $typeTranslateList = array_merge($typeTranslateList,array_diff(scandir($dir), array('..', '.')));
            }
            $typeTranslateList = array_unique ($typeTranslateList);
            $transInsert =  array();
            $i = 0;
            if(!empty($typeTranslateList)){
                foreach($typeTranslateList as $key=>$value){
                    $value = str_replace('.php', '', $value);
                    $transInsert[] = array(
                        'code'=>$value,
                        'comment'=>'',
                        'order_value'=>($i+1)
                    );
                    $i++;
                }
                SDB::table('sys_translate_type')->truncate();
                SDB::table('sys_translate_type')->insert($transInsert);
            }
            $resullt->status = SDBStatusCode::OK;
        }catch (\Exception $e){
            $resullt->status = SDBStatusCode::Excep;
            $resullt->message = $e->getMessage();
        }
        return $resullt;
    }
    protected function getTransType(){
        return SDB::table('sys_translate_type')->select()->get();
    }
    protected function getTranslateDataList($translateTypeCode, $lang):DataResultCollection{
        $data = new DataResultCollection();
        try{
            $result =  SDB::table('sys_translation')
                ->leftJoin('sys_translate_type','sys_translation.translate_type','=', 'sys_translate_type.code')
                ->whereRaw('sys_translate_type.code IS NOT NULL');
            if($translateTypeCode!=null && $translateTypeCode !=''){
                $result = $result->where('sys_translate_type.code','=',$translateTypeCode);
            }
            if($lang!=null && $lang !=''){
                $result = $result->where('sys_translation.lang_code','=',$lang);
            }
            $resultData= $result->selectRaw('sys_translation.id,sys_translation.lang_code,sys_translation.text,sys_translate_type.code AS translate_type_code ')->get();
            $data->status = SDBStatusCode::OK;
            $data->data = $resultData;
        }catch (\Exception $e){
            $data->status = SDBStatusCode::Excep;
            $data->message = $e->getMessage();
        }

        return $data;
    }

}
