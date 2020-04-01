<?php

namespace App\Api\V1\Services\Production;

use App\Api\V1\Services\Interfaces\CategoryServiceInterface;
use App\Core\Common\Category;
use App\Core\Common\CategoryConst;
use App\Core\Common\SDBStatusCode;
use App\Core\Dao\SDB;
use App\Core\Entities\DataResultCollection;
use App\Core\Helpers\CommonHelper;

class CategoryService implements CategoryServiceInterface
{
    public function homeCategory()
    {
        $result = new DataResultCollection();
        try {
            $type_normal  = CategoryConst::TYPE_NORMAL;
            $result->data = SDB::table("dtb_category")
                               ->where("parent_category_id",null)
                               ->where("del_flg","!=",CategoryConst::DEL_FLG)
                               ->orderBy("rank","desc")
                               ->selectRaw("category_id,category_name,rank,$type_normal as type")
                               ->get()
                               ->toArray();
            $maxRankData = max(array_column($result->data, 'rank'));
            $extraCategory[] = array('category_id'   => CategoryConst::NEW,
                                     'category_name' => trans("api.category.new"),
                                     'rank'          => $maxRankData + 1,
                                     'type'          => CategoryConst::TYPE_HTML
            );
            $extraCategory[] = array('category_id'   => CategoryConst::PICKUP,
                                     'category_name' => trans("api.category.pickup"),
                                     'rank'          => $maxRankData + 2,
                                     'type'          => CategoryConst::TYPE_HTML
            );
            $extraCategory[] = array('category_id'   => CategoryConst::HIGH_SCORE,
                                     'category_name' => trans("api.category.high_score"),
                                     'rank'          => $maxRankData + 5,
                                     'type'          => CategoryConst::TYPE_HTML
            );
            $extraCategory[] = array('category_id'   => CategoryConst::PROMOTION,
                                     'category_name' => trans("api.category.promotion"),
                                     'rank'          => $maxRankData + 4,
                                     'type'          => CategoryConst::TYPE_HTML
            );
            $extraCategory[] = array('category_id'   => CategoryConst::BIG_BOTTLE,
                                     'category_name' => trans("api.category.big_bottle"),
                                     'rank'          => $maxRankData + 3,
                                     'type'          => CategoryConst::TYPE_HTML
            );
            $result->data = (array)array_merge($extraCategory,$result->data);
//            usort($result->data, function( $a, $b) {
//                $a = (array) ($a);
//                $b = (array) $b;
//                return $a['rank'] <= $b['rank'];
//            });
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $exception) {
            $result->status  = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
            CommonHelper::CommonLog($exception->getMessage());
        }
        return $result;
    }
    
    public function allCategory()
    {
        $result = new DataResultCollection();
        try {
            $result->data = SDB::table("dtb_category")
                               ->where("del_flg","!=",CategoryConst::DEL_FLG)
                               ->orderBy("level","asc")
                               ->orderBy("rank","desc")
                               ->select("category_id","parent_category_id","category_name","level","rank")
                               ->get();
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $exception) {
            $result->status  = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
            CommonHelper::CommonLog($exception->getMessage());
        }
        return $result;
    }
}
