<?php

namespace App\Core\Helpers;

use App\Core\Dao\SDB;

class ProductHelper
{
    public static function queryGetImage()
    {
        $querySubImage = SDB::table("dtb_product_image")
                            ->groupBy("product_id")
                            ->selectRaw("product_id,min(dtb_product_image.rank) as i_rank");
        $arrProductImage = SDB::table("dtb_product_image as i")
                              ->joinSub($querySubImage, "image_group", function ($join) {
                                  $join->on("i.product_id", "=", "image_group.product_id");
                                  $join->on("i.rank", "=", "image_group.i_rank");
                              })
                              ->select("i.file_name", "i.product_id");
        return $arrProductImage;
    }

    public static function convertJsonRequest($request){
        $data         = $request->json()->all();
        $dataValidate = [];
        foreach ($data["products"] as $obj){
            $dataValidate["stock"][]              = (isset($obj["stock"])) ? $obj["stock"] : "";
            $dataValidate["delivery_date"][]      = (isset($obj["delivery_date"])) ? $obj["delivery_date"] : "";
            $dataValidate["product_class_id"][]   = (isset($obj["product_class_id"])) ? $obj["product_class_id"] : "";
            $dataValidate["class_category_id1"][] = (isset($obj["class_category_id1"])) ? $obj["class_category_id1"] : "";
            $dataValidate["class_category_id2"][] = (isset($obj["class_category_id2"])) ? $obj["class_category_id2"] : "";
        }
        return $dataValidate;
    }

    public static function convertJsonRequestOrder($request){
        $data         = $request->json()->all();
        $dataValidate = [];
        if(!empty($data['order_id'])){
            $dataValidate['order_id'] = $data['order_id'];
        }else{

            foreach ($data["products"] as $obj){
                $dataValidate["stock"][]              = (isset($obj["stock"])) ? $obj["stock"] : "";
                $dataValidate["delivery_date"][]      = (isset($obj["delivery_date"])) ? $obj["delivery_date"] : "";
                $dataValidate["product_class_id"][]   = (isset($obj["product_class_id"])) ? $obj["product_class_id"] : "";
            }
            if(!empty($data['customer'])) {
                foreach ($data['customer'] as $key => $entry) {
                    $dataValidate[$key] = $entry;
                }
            }
        }
        return $dataValidate;
    }
}
