<?php

namespace App\Core\Helpers;

/**
 * Created by PhpStorm.
 * User: my computer
 * Date: 6/30/2018
 * Time: 2:05 AM
 */

use App\Core\Common\DocumentConst;
use App\Core\Common\DocumentFileTypeConst;
use App\Core\Common\Pagging;
use App\Core\Common\Product;
use App\Core\Common\ProductConst;
use App\Core\Common\RoleConst;
use App\Core\Common\SDBStatusCode;
use App\Core\Common\StorageDisk;
use App\Core\Common\UserConst;
use App\Core\Common\VersionLogConst;
use App\Core\Common\VersionLogStatus;
use App\Core\Common\VersionLogType;
use App\Core\Common\VersionManagementConst;
use App\Core\Common\WaitingActionConst;
use App\Core\Dao\SDB;
use App\Core\Entities\DataResultCollection;
use App\Core\Jobs\ConvertImageFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class CommonHelper
{
    public static function CommonLog($message)
    {
        //Logging
        if (env('APP_DEBUG') == true) {
            abort($message);
        } else {
            Log::error($message);
        }
    }

    public static function getDefaultStorageDiskName()
    {
        return env('STORAGE_DISK_DEFAULT', 'public');
    }

    /**
     * @return ModuleInfor
     */
    public static function getCurrentModuleInfor(): ModuleInfor
    {
        $result = new ModuleInfor();
        try {
            $currentRoute = Route::getCurrentRoute();
            if ($currentRoute != null) {
                $curentActionInfo = $currentRoute->getAction();
                $module = strtolower(trim(str_replace('App\\', '', $curentActionInfo['namespace']), '\\'));
                $module = explode("\\", $module)[0];
                $_action = isset($curentActionInfo['controller']) ? explode('@', $curentActionInfo['controller']) : array();
                $_namespaces_chunks = isset($_action[0]) ? explode('\\', $_action[0]) : array();
                $controllers = strtolower(end($_namespaces_chunks));
                $action = strtolower(end($_action));
                $screenCode = $curentActionInfo['namespace'] . "\\" . $controllers . "\\" . $action;

                $result->module = $module;
                $result->controller = $controllers;
                $result->action = $action;
                $result->screenCode = $screenCode;
            }

        } catch (\Exception $ex) {
            //Dont handler here...
        }
        return $result;
    }

    /**
     * @return ModuleInfor
     */
    public static function getModuleInforByRouter($routerName): ModuleInfor
    {
        $result = new ModuleInfor();
        try {
            $currentRoute = Route::getRoutes()->getByName($routerName);
            if ($currentRoute != null) {
                $curentActionInfo = $currentRoute->getAction();
                $module = strtolower(trim(str_replace('App\\', '', $curentActionInfo['namespace']), '\\'));
                $module = explode("\\", $module)[0];
                $_action = isset($curentActionInfo['controller']) ? explode('@', $curentActionInfo['controller']) : array();
                $_namespaces_chunks = isset($_action[0]) ? explode('\\', $_action[0]) : array();
                $controllers = strtolower(end($_namespaces_chunks));
                $action = strtolower(end($_action));
                $screenCode = $curentActionInfo['namespace'] . "\\" . $controllers . "\\" . $action;

                $result->module = $module;
                $result->controller = $controllers;
                $result->action = $action;
                $result->screenCode = $screenCode;
            }

        } catch (\Exception $ex) {
            //Dont handler here...
        }
        return $result;
    }

    /*
     * @param: array routeName
     * @return: array module
     */
    public static function getModuleInforByMultiRouter($routeName)
    {
        $result = array();
        foreach ($routeName as $item) {
            $currentRoute = Route::getRoutes()->getByName($item);
            if ($currentRoute != null) {
                $curentActionInfo = $currentRoute->getAction();
                $module = strtolower(trim(str_replace('App\\', '', $curentActionInfo['namespace']), '\\'));
                $module = explode("\\", $module)[0];
                $_action = isset($curentActionInfo['controller']) ? explode('@', $curentActionInfo['controller']) : array();
                $_namespaces_chunks = isset($_action[0]) ? explode('\\', $_action[0]) : array();
                $controllers = strtolower(end($_namespaces_chunks));
                $action = strtolower(end($_action));
                $screenCode = $curentActionInfo['namespace'] . "\\" . $controllers . "\\" . $action;

                array_push($result, ['module' => $module, 'controller' => $controllers, 'action' => $action, 'screenCode' => $screenCode, 'routeName' => $item]);
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    public static function getExcelTemplatePath()
    {
        return base_path() . '/resources/export_templates/';
    }

    public static function isJSON($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) ? true : false;
    }

    //get Image Src
    public static function getListImageSrc($images)
    {
        foreach ($images as &$image) {
            $image->file_name = self::getImageSrc($image->file_name);
        }
        if(count($images) < 1){
            $images = ['file_name' => url('/') . "/common_images/no-image.png"];
        }
        return $images;
    }

    public static function getImageSrc($image = null, $default = null)
    {
        if ($image != null && $image != '') {
            $src = env('URL_IMAGE_FILE') . $image;
        } else {
            $src = ($default == null) ? url('/') . "/common_images/no-image.png" : url('/') . "/common_images/" . $default;
        }
        return $src;
    }

    //get Image Src
    public static function getAvatar($image)
    {
        $src = url('/') . "/common_images/no-avatar.png";
        if ($image != null && $image != '') {
            $diskLocalName = CommonHelper::getDefaultStorageDiskName();
            $src = Storage::disk($diskLocalName)->url($image);
        }
        return $src;

    }

    //get Image Url
    public static function getImageUrl($imageUri)
    {
        $diskLocalName = CommonHelper::getDefaultStorageDiskName();
        $imageUrl = Storage::disk($diskLocalName)->url($imageUri);
        return $imageUrl;
    }

    /**
     * @param $data
     * @return array
     * ex:
     * inp:
     * [
     *      key1=>[
     *          key2=>a,
     *          key3=>b,
     *     ],
     *     key4=> c
     * ]
     *  out:
     * [
     *      key1.key2=>a,
     *      key1.key3=>b,
     *      key4=>c
     * ]
     */
    public static function flatten($array, $prefix = '')
    {
        $delimiter = ".";
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = $result + self::flatten($value, $prefix . $key . $delimiter);
            } else {
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }

    /**
     * @param $input
     * @return array
     */
    public static function array_non_empty_items($input)
    {
        // If it is an element, then just return it
        if (!is_array($input)) {
            return $input;
        }
        $non_empty_items = array();

        foreach ($input as $key => $value) {
            // Ignore empty cells
            if ((is_array($value) && !empty($value)) || (!is_array($value) && $value)) {
                // Use recursion to evaluate cells
                $non_empty_items[$key] = self::array_non_empty_items($value);
                if (empty($non_empty_items[$key])) {
                    unset($non_empty_items[$key]);
                }
            }
        }

        // Finally return the array without empty items
        return $non_empty_items;
    }

    /**
     * @param $fileList
     * @param $diskName //Disk name in config/filesystem
     * @param $subFolder //Subfolder
     * @param $option //option for cloud upload
     * @return DataResultCollection
     */
    public static function uploadFile($fileList, $diskName, $subFolder, $option): DataResultCollection
    {
        $result = new DataResultCollection();
        $result->status = SDBStatusCode::OK;
        $result->data = array();
        //NOTE : This will store file to path with: root path has config in config/filesystems.php, sub folder is $subFolder
        if (is_array($fileList) && !empty($fileList)) {
            foreach ($fileList as $item) {
                $path = Storage::disk($diskName)->put($subFolder, $item, $option);
                $fileInfor = array(
                    'client_file_name' => $item->getClientOriginalName(),
                    'uri' => $path,
                    'url' => Storage::disk($diskName)->url($path)
                );
                $result->data[] = $fileInfor;
            }
        }
        return $result;
    }

    /**
     * @param $diskName
     * @param $filePath
     * @return DataResultCollection
     */
    public static function deleteFile($diskName, $filePath): DataResultCollection
    {
        $result = new DataResultCollection();
        $result->status = SDBStatusCode::OK;
        $result->data = array();
        Storage::disk($diskName)->delete($filePath);
        return $result;
    }

    /**
     * @param $arrayDeviveToken array[string] device token or only one device token as a string
     * @param $title
     * @param $body
     * @param $data array(key=>value)
     * @author thanhnv
     */
    public static function pushNotification($arrayDeviveToken, $title, $body, $data): DataResultCollection
    {
        $result = new DataResultCollection();
        try {
            if (!empty($arrayDeviveToken)) {
                $optionBuilder = new OptionsBuilder();
                $optionBuilder->setTimeToLive(60 * 20);

                $notificationBuilder = new PayloadNotificationBuilder($title);
                $notificationBuilder->setBody($body)
                    ->setSound('default');

                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData($data);

                $option = $optionBuilder->build();
                $notification = $notificationBuilder->build();
                $data = $dataBuilder->build();

                FCM::sendTo($arrayDeviveToken, $option, $notification, $data);
            }
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $e) {
            $result->status = SDBStatusCode::Excep;
            $result->message = $e->getMessage();
            Log::error($e->getMessage());
        }
        return $result;
    }

    /**
     * Common log Exception
     *
     * @param \Exception $exception
     * @param bool $returnMsg
     * @return string
     */
    public static function logException(\Exception $exception, $returnMsg = true)
    {
        $msg = $exception->getMessage() . ' on ' . $exception->getFile() . ' Line ' . $exception->getLine();
        Log::error($msg);

        if ($returnMsg) {
            return $msg;
        }
    }

    /**
     * @param $filename ex: a.pdf
     * @return mixed
     */
    public static function getStringName($filename)
    {
        $info = pathinfo($filename);
        return array_get($info, 'filename');
    }


    public static function getExceptionError(\Exception $e)
    {
        if (env('APP_DEBUG') == true) {
            return $e->getMessage() . " in line " . $e->getLine() . "-" . $e->getFile() . "\n" . $e->getTraceAsString();
        } else {
            return "Has error";
        }
    }

    /**
     * @param $filePath
     * @param $fileContent
     * @param string $permission
     * @return bool
     * @throws \Exception
     */
    public static function putToS3($filePath, $fileContent, $permission = 'public')
    {
        return Storage::disk('s3')->put($filePath, $fileContent, $permission);
    }

    /**
     * @param $filename
     */
    public static function deleteFileS3($filePath)
    {
        return Storage::disk('s3')->delete($filePath);
    }

    public static function getListFileOffline($folderPath, $extension)
    {
        $list = null;
        if (is_dir($folderPath)) {
            $list = array_slice(preg_grep('~\.(' . $extension . ')$~', scandir($folderPath)), 0);
        } else {
            Log::error('Path not exists ...');
        }
        return $list;
    }

    public static function curl($url, $isJson = false)
    {
        $referer = "";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate,sdch");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Requested-With: XMLHttpRequest']);
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        $response = curl_exec($ch);

        if ($isJson) {
            return json_decode($response, true);
        }

        return $response;
    }
    
    public static function dateNow($format=null){
        $now = Carbon::now(env('APP_TIME_ZONE', 'Asia/Ho_Chi_Minh'));
        $now = ($format!=null)?$now->format($format):$now->format('Y-m-d H:i:s');
        return $now;
    }

    public static function dateExpite($timeExpire = null)
    {
        $time = date('Y-m-d H:i:s', time() + $timeExpire);
        return $time;
    }

    public static function timeExpiteToDate($time)
    {
        return date('Y-m-d', time() + $time);
    }

    public static function getCurrentUTC($format = null)
    {
        $now = Carbon::now('UTC');
        $now = ($format != null) ? $now->format($format) : $now->format('Y-m-d H:i:s');
        return $now;
    }

    //get locat timezone from utc
    public static function getLocalTime($time)
    {
        $time = strtotime($time . ' UTC');
        $dateInLocal = date(ChatConst::DATE_FORMAT_SERVER, $time);
        return $dateInLocal;
    }

    public static function getLocalTimeByUTC($format = null)
    {
        $now = Carbon::now('UTC');
        $time = strtotime($now . ' UTC');
        $dateInLocal = ($format != null) ? date($format, $time) : date(ChatConst::DATE_FORMAT_SERVER, $time);
        return $dateInLocal;
    }

    public static function convertDateTimeFormat($date, $format = null)
    {
        if ($date != null) {
            if ($format == null) {
                $date = date_create($date);
                $date = date_format($date, DateConst::DATE_FORMAT_SER);
            } else {
                $date = date_create($date);
                $date = date_format($date, $format);
            }
        }
        return $date;
    }

    public static function getProductImage($ids)
    {
        return SDB::table('dtb_product_image')
            ->whereIn('product_id', $ids)
            ->select('file_name')
            ->orderBy('rank', 'ASC')
            ->get();
    }

    public static function getProduct($ids)
    {
        return SDB::table('dtb_product')
            ->whereIn('dtb_product.product_id', $ids)
            ->where('dtb_product.del_flg', UserConst::ENABLED)
            ->where('dtb_product.status', ProductConst::STATUS_PUBLIC);
    }
    
    public static function getProductPrice($price_from=null,$price_to=null,$optional=""){
        if($price_from!=$price_to && ($price_from!=null&$price_to!=null)){
            $price = $price_from." ～ ".($price_to);
        }else{
            $price = $price_from;
        }
        return $price.$optional;
    }
    
    public static function getPriceAfterDiscont($price=0,$discount=0,$tax=0)
    {
        return ($price+$tax)*(1-$discount*0.01);
    }

    public static function getProductTag($ids)
    {
        return self::getProduct($ids)
            ->select('mtb_tag.id', 'mtb_tag.name')
            ->leftjoin('dtb_product_tag', function ($join) {
                $join->on('dtb_product.product_id', 'dtb_product_tag.product_id');
            })->join('mtb_tag', function ($join) {
                $join->on('dtb_product_tag.tag', 'mtb_tag.id');
            })->get();
    }

    public static function getProductClass($ids)
    {
        $ProductClass = SDB::table('dtb_product_class as p_class')
            ->where('p_class.del_flg', UserConst::ENABLED)
            ->whereIn('p_class.product_id', $ids)
            ->join('dtb_class_category as c_category', 'p_class.class_category_id1', '=', 'c_category.class_category_id')
            ->select('c_category.name', 'c_category.class_category_id')
            ->where(function ($query){
                $query->where('stock','>',0)
                    ->orwhere('stock_unlimited',ProductConst::STOCK_UNLIMITED);
            })
            ->distinct()
            ->get();
        if (count($ProductClass) < 1) {
            $ProductClass = SDB::table('dtb_product_class as p_class')
                ->where('p_class.del_flg', UserConst::ENABLED)
                ->whereIn('p_class.product_id', $ids)
                ->select('p_class.product_code', 'p_class.stock', 'p_class.sale_limit', 'p_class.price01',
                    'p_class.price02','p_class.product_class_id')
                ->first();
            $ProductClass->price01 = CommonHelper::getProductPrice($ProductClass->price01);
            $ProductClass->price02 = CommonHelper::getProductPrice($ProductClass->price02);
        }
        return $ProductClass;

    }


    public static function checkFavoriteProduct($id)
    {
        return SDB::table('dtb_customer_favorite_product')
            ->where('customer_id', Auth::guard("api")->id())
            ->where('product_id', $id)
            ->where('del_flg', UserConst::ENABLED)
            ->exists();
    }


    public static function getCategoryRelate($id)
    {
        $category = SDB::table('dtb_product_category')
            ->where('product_id', $id)
            ->leftjoin('dtb_category as category1', function ($join) {
                $join->on('category1.category_id', 'dtb_product_category.category_id');
            })
            ->get();
        $resultCategoryRelate = [];
        if(count($category) > 0) {
            $levelMax = 0;
            $categoryRelateIds = [];
            foreach ($category as $entry) {
                $categoryRelateIds[] = $entry->category_id;
                if ($entry->level > $levelMax) {
                    $levelMax = $entry->level;
                }
            }
            $categoryRelate = SDB::table('dtb_category as c1')
                ->whereIn('c1.category_id', $categoryRelateIds);
            $select = '';
            for ($i = 1; $i <= $levelMax; $i++) {
                $temp = $i + 1;
                $rank = $levelMax - $i + 1;
                $select .= 'c' . $rank . '.category_id as category_id_rank' . $rank . ',c' .
                    $rank . '.category_name as category_name_rank' . $rank . '';
                if ($i != $levelMax) {
                    $select .= ', ';
                }

                if ($i < $levelMax) {
                    $categoryRelate->leftjoin('dtb_category as c' . $temp, function ($join) use ($temp, $i) {
                        $join->on('c' . $temp . '.category_id', 'c' . $i . '.parent_category_id');
                    });
                }
            }
            $result = $categoryRelate->selectRaw($select)->get()->toArray();
            foreach ($result as $entry) {
                $data = [];
                $entry = json_decode(json_encode($entry), True);
                $i = 0;
                foreach ($entry as $key => $value) {
                    $i++;
                    if ($value == null) {
                        unset($entry[$key]);
                    } else {
                        if ($i % 2 == 1) {
                            $temp = new \stdClass();
                            $temp->category_id = $value;
                        }
                        if ($i % 2 == 0) {
                            $temp->name = $value;
                            $data[] = $temp;
                        }
                    }
                }
                $resultCategoryRelate[] = $data;
            }
        }
        return $resultCategoryRelate;
    }
    
    public static function unset_multikey($array_result = [],$remove=[])
    {
        if(is_array($array_result)){
            $array_result = array_diff_key($array_result, array_flip($remove));
            return $array_result;
        }else{
            $array_result = array_diff_key((array) $array_result, array_flip($remove));
            return (object) $array_result;
        }
    }


}
