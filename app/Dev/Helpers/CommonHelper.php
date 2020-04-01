<?php
/**
 * Created by PhpStorm.
 * User: MSI
 * Date: 6/21/2018
 * Time: 9:32 AM
 */

namespace App\Dev\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Core\Common\SDBStatusCode;

class CommonHelper
{
    /**
     * @param $dataArray
     * @return string
     */
    public static function generateResponeJSON($dataArray){
        $result = array(
            'status'=>array(),
            'data'=>array()
        );
        $count= 0;
        if(isset($dataArray) && is_array($dataArray))
            $count = count($dataArray);
        if($count>=1){
            $result['status'] = $dataArray[0];
        }
        for($i=1;$i<$count;$i++){
            $result['data'][] =$dataArray[$i];
        }
        return json_encode($result);
    }

    /**
     * @param $error
     * @return array
     */
    public static function convertVaidateErrorToCommonStruct($error){
        $result = array(
            array(
                'code'=>SDBStatusCode::WebError,
                'data_error'=>json_encode($error)
            )
        );

        return $result;
    }
    public static function CommonLog($message){
        //Logging
        if(env('APP_DEBUG')==true){
            abort($message);
        }else{
            Log::error($message);
        }
    }
    public static function isJSON($string){
        return is_string($string) && is_array(json_decode($string, true)) ? true : false;
    }
    public static function flatten($array, $prefix = '') {
        $delimiter = ".";
        $result = array();
        foreach($array as $key=>$value) {
            if(is_array($value)) {
                $result = $result + self::flatten($value, $prefix . $key . $delimiter);
            }else {
                $result[$prefix.$key] = $value;
            }
        }
        return $result;
    }
    public static function array_non_empty_items($input) {
        // If it is an element, then just return it
        if (!is_array($input)) {
            return $input;
        }
        $non_empty_items = array();

        foreach ($input as $key => $value) {
            // Ignore empty cells
            if((is_array($value)  && !empty($value))||(!is_array($value) && $value)) {
                // Use recursion to evaluate cells
                $non_empty_items[$key] = self::array_non_empty_items($value);
                if(empty($non_empty_items[$key])){
                    unset($non_empty_items[$key]);
                }
            }
        }

        // Finally return the array without empty items
        return $non_empty_items;
    }
}
