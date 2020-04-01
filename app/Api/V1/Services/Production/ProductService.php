<?php

namespace App\Api\V1\Services\Production;

use App\Api\V1\Services\Interfaces\ProductServiceInterface;
use App\Core\Common\CalenderConst;
use App\Core\Common\Category;
use App\Core\Common\CategoryConst;
use App\Core\Common\Pagging;
use App\Core\Common\Product;
use App\Core\Common\ProductConst;
use App\Core\Common\SDBStatusCode;
use App\Core\Common\SysConst;
use App\Core\Common\UserConst;
use App\Core\Dao\SDB;
use App\Core\Entities\DataResultCollection;
use App\Core\Helpers\AuthHelper;
use App\Core\Helpers\CommonHelper;
use App\Core\Helpers\ProductHelper;
use App\Core\Helpers\TaxHelper;
use App\Core\Jobs\SendMailContact;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductService implements ProductServiceInterface
{
    public function getAll($request)
    {
        $result       = new DataResultCollection();
        $perPage      = $request->input("disp_number", Pagging::API_PER_PAGE);
        $category     = $request->input("category_id", null);
        $orderBy      = $request->input("orderby", null);
        $key          = $request->input("name", null);
        $tag          = $request->input("tag_id", null);
        try {
            $arrProductImage = ProductHelper::queryGetImage();
            $arrProduct = SDB::table("dtb_product as p")
                             ->leftJoinSub($arrProductImage, "i", "i.product_id", "=", "p.product_id")
                             ->leftJoin("dtb_product_class as p_class", function ($join) {
                                $join->on('p_class.product_id', '=', 'p.product_id');
                                $join->where("p_class.del_flg", ProductConst::NOT_DELETE);
                                $join->where(function ($query) {
                                    $query->where("p_class.stock", ">", 0);
                                    $query->orWhere("p_class.stock_unlimited", ProductConst::STOCK_UNLIMITED);
                                });
                             })
                             ->where("p.status", ProductConst::PUBLISH)
                             ->where("p.del_flg", ProductConst::NOT_DELETE)
                             ->selectRaw("p.product_id,p.name,i.file_name,min(p_class.price02) as price_from,
                                            max(p_class.price02) as price_to");
            if ($category != null) {
                $tmp = SDB::table("dtb_category")
                    ->where("del_flg", CategoryConst::NOT_DELETE)
                    ->where("category_id", $category)
                    ->pluck("category_id")
                    ->toArray();
                $arrCategory = [];
                while (!empty($tmp)) {
                    $arrCategory = array_merge($arrCategory, $tmp);
                    $tmp = SDB::table("dtb_category")
                        ->where("del_flg", CategoryConst::NOT_DELETE)
                        ->whereIn("parent_category_id", $tmp)
                        ->pluck("category_id")
                        ->toArray();
                }
                $arrProduct->join("dtb_product_category as pc", function ($join) use ($arrCategory) {
                    $join->on("p.product_id", "=", "pc.product_id");
                    $join->whereIn("pc.category_id", $arrCategory);
                });
            }
            $arrProduct->join("plg_sort_product as s_p", "s_p.product_id", "=", "p.product_id");
            if ($key != null) {
                $schema_info = SDB::selectOne("SELECT @@character_set_database as charset, @@collation_database as collection");
                $keywords    = preg_split('/[\s　]+/u', $key, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($keywords as $index => $keyword) {
                    $arrProduct ->where(function ($query) use ($keyword,$schema_info) {
                        $query  ->whereRaw("CONVERT(p.name USING $schema_info->charset) COLLATE $schema_info->collection LIKE CONVERT('%$keyword%' USING $schema_info->charset) COLLATE $schema_info->collection")
                                ->orWhereRaw("CONVERT(p.search_word  USING $schema_info->charset) COLLATE $schema_info->collection LIKE CONVERT('%$keyword%' USING $schema_info->charset) COLLATE $schema_info->collection");
                    });
                }
            };
            switch ($orderBy) {
                case ProductConst::PRICE_LOWER:
                    {
                        $arrProduct->where(function ($query) {
                            $query->where("p_class.stock", ">", 0);
                            $query->orWhere("p_class.stock_unlimited", ProductConst::STOCK_UNLIMITED);
                        })
                            ->orderBy("price_from", "asc");
                        break;
                    }
                case ProductConst::PRODUCT_NEWER:
                    {
                        $arrProduct->where(function ($query) {
                            $query->where("p_class.stock", ">", 0);
                            $query->orWhere("p_class.stock_unlimited", ProductConst::STOCK_UNLIMITED);
                        })
                            ->orderBy("p.create_date", "desc");
                        break;
                    }
                case ProductConst::PRICE_HIGHER:
                    {
                        $arrProduct->where(function ($query) {
                            $query->where("p_class.stock", ">", 0);
                            $query->orWhere("p_class.stock_unlimited", ProductConst::STOCK_UNLIMITED);
                        })
                            ->orderBy("price_to", "desc");
                        break;
                    }
                default:
                    {
                            $arrProduct ->selectRaw("ifnull(max(s_p.rank),0) as s_p_rank")
                                        ->orderBy("s_p_rank", "desc");
                        break;
                    }
            }
            $arrProduct = $arrProduct   ->orderBy("p.product_id", "desc")
                                        ->groupBy("p.product_id", "p.name", "p.create_date", "i.file_name")
                                        ->paginate($perPage);
            foreach ($arrProduct as $obj) {
                $obj->file_name = CommonHelper::getImageSrc($obj->file_name);
                $obj->price     = CommonHelper::getProductPrice($obj->price_from, $obj->price_to);
                unset($obj->price_from);
                unset($obj->price_to);
            }
            $result->data         = $arrProduct->items();
            $result->current_page = $arrProduct->currentPage();
            $result->last_page    = $arrProduct->lastPage();
            $result->message      = (empty($arrProduct->items())) ? trans("label.empty_data") : "";
            $result->status       = SDBStatusCode::OK;
        } catch (\Exception $exception) {
            $result->status = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
            CommonHelper::CommonLog($exception->getMessage());
        }
        return $result;
    }

    public function getProductFromHtml($request)
    {
        $category_id = $request->input('category_id', CategoryConst::PICKUP);
        $client = new Client(['base_uri' => env("URL_BASE_INDEX")]);
        $result = new DataResultCollection();
        try {
            switch ($category_id) {
                case CategoryConst::NEW:
                    {
                        $url = "";
                        $query = '//div[contains(concat(" ",normalize-space(@class)," ")," newarrival_area ")]//div[contains(concat(" ",normalize-space(@class)," ")," pickup_item ")]//a';
                        break;
                    }
                case CategoryConst::PICKUP:
                    {
                        $url = "";
                        $query = '//div[contains(concat(" ",normalize-space(@class)," "),"bnr_area")]//div[contains(concat(" ",normalize-space(@class)," ")," pickup_item ")]//a';
                        break;
                    }
                case CategoryConst::HIGH_SCORE:
                    {
                        $url = "user_data/high-score";
                        $query = "//div[@class='pickup_item']/a";
                        break;
                    }
                case CategoryConst::PROMOTION:
                    {
                        $url = "user_data/promotion";
                        $query = "//div[@class='pickup_item']/a";
                        break;
                    }
                case CategoryConst::BIG_BOTTLE:
                    {
                        $url = "user_data/big-bottle";
                        $query = "//div[@class='pickup_item']/a";
                        break;
                    }
                default:
                    {
                        $url = "";
                        $query = '//div[contains(concat(" ",normalize-space(@class)," ")," newarrival_area ")]//div[contains(concat(" ",normalize-space(@class)," ")," pickup_item ")]//a';
                        break;
                    }
            }
            try {
                $curl = $client->get($url);
                if ($curl->getReasonPhrase() == SDBStatusCode::OK) {
                    $response = $curl->getBody()->getContents();
                    $html     = preg_replace('#<(keygen|source|track|wbr)(?=\s|>)("[^"]*"|\'[^\']*\'|[^"\'>]+)*+(?<!/)>#', '<$1$2 />', $response);
                    libxml_use_internal_errors(TRUE);
                    $dom = new \DOMDocument();
                    $dom->loadHTML($html);
                    $xpath   = new \DOMXPath($dom);
                    $pickups = $xpath->query($query);
                    foreach ($pickups as $pickup) {
                        $parts          = explode("/", $pickup->getAttribute('href'));
                        $arrProductId[] = end($parts);
                    }
                    if (!empty($arrProductId)) {
                        $arrProductImage = ProductHelper::queryGetImage();
                        $arrProduct      = SDB::table("dtb_product as p")
                                                ->leftJoinSub($arrProductImage, "i", "i.product_id", "=", "p.product_id")
                                                ->leftJoin("dtb_product_class as p_class", function ($join) {
                                                    $join->on('p_class.product_id', '=', 'p.product_id');
                                                    $join->where("p_class.del_flg", ProductConst::NOT_DELETE);
                                                    $join->where(function ($query) {
                                                        $query->where("p_class.stock", ">", 0);
                                                        $query->orWhere("p_class.stock_unlimited", ProductConst::STOCK_UNLIMITED);
                                                    });
                                                })
                                                ->where("p.status", ProductConst::PUBLISH)
                                                ->where("p.del_flg", ProductConst::NOT_DELETE)
                                                ->whereIn("p.product_id", $arrProductId)
                                                ->selectRaw("p.product_id,p.name,i.file_name,min(p_class.price02) as price_from,
                                max(p_class.price02) as price_to")
                                                ->groupBy("p.product_id", "p.name", "p.create_date", "i.file_name")
                                                ->get();
                        foreach ($arrProduct as $obj) {
                            $obj->file_name = CommonHelper::getImageSrc($obj->file_name);
                            $obj->price     = CommonHelper::getProductPrice($obj->price_from, $obj->price_to);
                            unset($obj->price_from);
                            unset($obj->price_to);
                        }
                        $result->data = $arrProduct;
                    }
                }
            } catch (ClientException $e) {
                $result->data    = [];
                $result->message = $e->getResponse()->getReasonPhrase();
                Log::error($e->getResponse()->getReasonPhrase());
            }
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $exception) {
            $result->status  = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
            CommonHelper::CommonLog($exception->getMessage());
        }
        return $result;
    }

    public static function getCart($request)
    {
        $result         = new DataResultCollection();
        $result->status = SDBStatusCode::OK;
        if ($request->input('is_order') == null) {
            $data = ProductHelper::convertJsonRequest($request);
        } else {
            $data = $request->all();
        }
        try {
            $arr_product_class_id = $data["product_class_id"];
            $arr_stock            = $data["stock"];
            $arr_delivery_date    = $data["delivery_date"];
            $arrProductImage      = ProductHelper::queryGetImage();
            $products = SDB::table("dtb_product_class as p_class")
                            ->join("dtb_product as p", "p.product_id", "=", "p_class.product_id")
                            ->leftJoinSub($arrProductImage, "i", function ($join) {
                                $join->on("i.product_id", "p.product_id");
                            })
                            ->leftJoin("dtb_class_category as c_category1", "c_category1.class_category_id", "=", "p_class.class_category_id1")
                            ->leftJoin("dtb_class_category as c_category2", "c_category2.class_category_id", "=", "p_class.class_category_id2")
                            ->leftJoin("plg_customerrank_dtb_price as r_price", function ($join) {
                                $join->on("r_price.product_class_id", "=", "p_class.product_class_id");
                                $join->on("r_price.product_id", "=", "p_class.product_id");
                                $join->where("r_price.customer_rank_id", AuthHelper::getRank());
                            })
                            ->leftJoin("plg_customerrank_dtb_customer_rank as c_rank", function ($join) {
                                $join->where("c_rank.customer_rank_id", AuthHelper::getRank());
                                $join->where("c_rank.del_flg", SysConst::NOT_DEL_FLG);
                            })
                            ->selectRaw("p_class.product_class_id,p_class.product_id,p_class.product_code,p_class.stock,
                p_class.stock_unlimited,p_class.stock_unlimited,r_price.price,p_class.price02,p_class.sale_limit,
                p.name,i.file_name,c_category1.name as class_category_id1,c_category2.name as class_category_id2,
                ifnull(c_rank.discount_rate,0) as discount_rate,ifnull(c_rank.discount_value,0) as discount_value")
                            ->whereIn("p_class.product_class_id", $arr_product_class_id)
                            ->where("p_class.del_flg", SysConst::NOT_DEL_FLG)
                            ->get();
            $productOutStock = [];
            $totalPrice = 0;
            if (!empty($products->toArray())) {
                foreach ($products as $index_p => $product) {
                    $out_of_stock      = false;
                    $taxRule           = TaxHelper::getTaxRule($product->product_class_id);
                    $product->tax_rate = $taxRule->tax_rate;
                    $product->tax_rule = $taxRule->calc_rule;
                    $product->tax      = TaxHelper::calcTax($product->price02, $taxRule->tax_rate, $taxRule->calc_rule, $taxRule->tax_adjust);
                    if ($product->price != null) {
                        $tax                    = TaxHelper::calcTax($product->price, $taxRule->tax_rate, $taxRule->calc_rule, $taxRule->tax_adjust);
                        $product->price_display = CommonHelper::getPriceAfterDiscont($product->price, 0, $tax);
                        $product->price         = CommonHelper::getPriceAfterDiscont($product->price, 0, $tax);
                    } else {
                        $tax                    = TaxHelper::calcTax($product->price02, $taxRule->tax_rate, $taxRule->calc_rule, $taxRule->tax_adjust);
                        $product->price_display = CommonHelper::getPriceAfterDiscont($product->price02, $product->discount_rate, $tax);
                        $product->price         = CommonHelper::getPriceAfterDiscont($product->price02, $product->discount_rate, $tax);
                    }
                    $product->tax       = CommonHelper::getProductPrice($product->tax);
                    $class_category_id1 = ($product->class_category_id1 != null) ? "-" .
                                                                                   $product->class_category_id1 : '';
                    $class_category_id2 = ($product->class_category_id2 != null) ? "-" .
                                                                                   $product->class_category_id2 : '';
                    foreach ($arr_product_class_id as $index => $p_class_id) {
                        if ($p_class_id == $product->product_class_id) {
                            $product->delivery_date = $arr_delivery_date[$index];
                            $product->file_name     = CommonHelper::getImageSrc($product->file_name);
                            if ($product->stock_unlimited != ProductConst::STOCK_UNLIMITED && $product->stock == 0) {
                                $productOutStock[] = $product->product_class_id;
                            }
                            if ($product->stock_unlimited == ProductConst::STOCK_UNLIMITED) {
                                if ($product->sale_limit < $arr_stock[$index] && $product->sale_limit != null) {
                                    $product->stock    = $product->sale_limit;
                                    $result->message[] = trans("api.product.warning_sale_limit", ['name' => $product->name, 'class_category1' => $class_category_id1, 'class_category2' => $class_category_id2]);
                                } else {
                                    $product->stock = $arr_stock[$index];
                                }
                            } else if ($product->stock == 0) {
                                $result->message[] = trans("api.product.warning_product_out_of_stock", ['name' => $product->name, 'class_category1' => $class_category_id1, 'class_category2' => $class_category_id2]);
                                if ($request->input('is_order') == null) {
                                    unset($products[$index_p]);
                                }
                                $out_of_stock = true;
                            } else if ($product->stock < $arr_stock[$index]) {
                                if($product->sale_limit < $arr_stock[$index] && $product->sale_limit!=null){
                                    $product->stock    = $product->sale_limit;
                                }
                                $result->message[] = trans("api.product.warning_product_stock", ['name' => $product->name, 'class_category1' => $class_category_id1, 'class_category2' => $class_category_id2]);
                            } else if ($product->sale_limit < $arr_stock[$index] && $product->sale_limit != null) {
                                $product->stock    = $product->sale_limit;
                                $result->message[] = trans("api.product.warning_sale_limit", ['name' => $product->name, 'class_category1' => $class_category_id1, 'class_category2' => $class_category_id2]);
                            } else {
                                $product->stock = $arr_stock[$index];
                                $result->status = SDBStatusCode::OK;
                            };
                            if ($request->input('is_order') != null) {
                                $product->stock     = $arr_stock[$index];
                                $remove             = [
                                    'stock_unlimited',
                                    'sale_limit'
                                ];
                                $products[$index_p] = CommonHelper::unset_multikey($product, $remove);
                            } else {
                                if (!$out_of_stock) {
                                    $remove             = [
                                        'stock_unlimited',
                                        'sale_limit'
                                    ];
                                    $products[$index_p] = CommonHelper::unset_multikey($product, $remove);
                                }
                            }
                        }
                    }
                    $totalPrice += $product->price;
                }
            }
            if (empty($products->toArray())) {
//                $result->message[] = trans("common.empty_cart");
            }else{
                $result->total_price = $totalPrice;
            }

            if ($result->message && is_array($result->message)) {
                $result->message = array_unique($result->message);

            }
            $result->data = array_values($products->toArray());
            if ($request->input('is_order') != null && count($productOutStock) == count($products)) {
                $result->status = SDBStatusCode::Excep;
            }
        } catch (\Exception $exception) {
            $result->status  = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
            CommonHelper::CommonLog($exception->getMessage());
        }
        return $result;
    }
    
    public function addToFavorite($id)
    {
        $result          = new DataResultCollection();
        $productFavorite = SDB::table('dtb_customer_favorite_product')
                                ->where('customer_id', Auth::id())
                                ->where('product_id', $id)
                                ->where('del_flg', SysConst::NOT_DEL_FLG)
                                ->first();
        $delFlg = SysConst::NOT_DEL_FLG;
        $updateDate = $createDate = CommonHelper::dateNow();
        if ($productFavorite) {
            $delFlg     = !$productFavorite->del_flg;
            $createDate = $productFavorite->create_date;
        }
        SDB::table('dtb_customer_favorite_product')
            ->updateOrInsert(
                ['customer_id' => Auth::id(), 'product_id' => $id],
                ['del_flg' => $delFlg, 'create_date' => $createDate, 'update_date' => $updateDate]
            );
        $result->status = SDBStatusCode::OK;
        return $result;
    }

    public function addToCart($request)
    {
        $result = $this->getCart($request);
        return $result;
    }

    public function contact($request)
    {
        $result = new DataResultCollection();
        try {
            $param         = $request->all();
            $param['name'] = $param['name01'] . $param['name02'];
            if (!empty($param['kana01']) || !empty($param['kana02'])) {
                $param['name'] .= '(' . $request->input('kana01', null) . $request->input('kana01', null) . ')';
            }
            $param['name'] .= '様';
            $param['zip']  = (!empty($param['zip01']) && !empty($param['zip02'])) ? '〒' . $param['zip01'] . ' - ' .
                                                                                    $param['zip02'] : "";
            $param['addr'] = $request->input('pref', null) . $request->input('addr01', null) .
                             $request->input('addr02', null);
            $param['tel']  = $request->input('tel01', null) . ' - ' . $request->input('tel02', null) . ' - ' .
                             $request->input('tel03', null);
            SendMailContact::dispatch($param);
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $exception) {
            $result->status = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
            CommonHelper::CommonLog($exception->getMessage());
        }
        return $result;
    }

    public function detail($id = null)
    {
        $result         = new DataResultCollection();
        $categoryRelate = CommonHelper::getCategoryRelate($id);
        $productClass   = SDB::table('dtb_product_class')
                                ->where('del_flg', UserConst::ENABLED)
                                ->where(function ($query) {
                                    $query  ->where('stock', '>', 0)
                                            ->orWhere('stock_unlimited', ProductConst::STOCK_UNLIMITED);
                                });
        $product = SDB::table('dtb_product as p')
                        ->where('p.product_id', $id)
                        ->where('p.del_flg', UserConst::ENABLED)
                        ->where('p.status', ProductConst::STATUS_PUBLIC)
                        ->leftJoinSub($productClass, 'pc', 'p.product_id', '=', 'pc.product_id');
        if ($productClass->count() == 1) {
            $product = $product->selectRaw("p.product_id, p.name,p.search_word, p.free_area, p.note, 
            description_list, description_detail,pc.price01 as price01_min, pc.price01 as price01_max , 
            pc.price02 as price02_min, pc.price02 as price02_max, pc.product_code, pc.product_class_id");
        } else {
            $product = $product ->selectRaw("p.product_id, p.name,p.search_word,p.free_area,p.note, description_list,
            description_detail, min(pc.price01) as price01_min, max(pc.price01) as price01_max,
             min(pc.price02) as price02_min, max(pc.price02) as price02_max, min(pc.product_code) as product_code")
                                ->groupBy('p.product_id', 'p.name', 'p.note', 'p.free_area',
                    'p.search_word', 'description_list', 'description_detail');
        }
        $product = $product->first();
        if ($product) {
            if ($product->price01_max > 0) {
                $product->price01 = CommonHelper::getProductPrice($product->price01_min, $product->price01_max);
            } else {
                $product->price01 = "";
            }
            $product->price02 = CommonHelper::getProductPrice($product->price02_min, $product->price02_max);
            $product->text_tax = trans('label.tax');
            $remove = [
                'price01_min',
                'price01_max',
                'price02_min',
                'price02_max'
            ];
            $product = CommonHelper::unset_multikey($product, $remove);
            $productFavorite = false;
            if (Auth::guard('api')->check()) {
                $productFavorite = CommonHelper::checkFavoriteProduct($id);
            }
            $selectionCalendar = SDB::table('plg_selection_calender')
                                        ->where('display_calender', CalenderConst::DISPLAY_CALENDER)
                                        ->select('display_name', 'display_month', 'shipment_days')
                                        ->where('del_flg', UserConst::ENABLED)
                                        ->first();
            $selectionCalendarTemp = new \stdClass();
            if ($selectionCalendar) {
                $start                      = new Carbon(date('Y/m/d', time()));
                $product->display_calender  = true;
                $time                       = $start->addDays($selectionCalendar->shipment_days);
                $selectionCalendarTemp->min = date('Y/m/d', strtotime($time));
                if ($selectionCalendar->display_month <= 1) {
                    $selectionCalendar->display_month = 2;
                }
                if (date('m', strtotime($time)) != date('m', time()) && $selectionCalendar->display_month == 2){
                    $time                       = $time->addMonthsNoOverflow($selectionCalendar->display_month - 1);
                    $selectionCalendarTemp->max = date('Y/m/d', strtotime($time) - 86400);
                }else{
                    $time                       = $time->addMonthsNoOverflow($selectionCalendar->display_month - 1);
                    $selectionCalendarTemp->max = date('Y/m/d', strtotime($time));
                }
            } else {
                $product->display_calender = false;
            }
            $productImage                = CommonHelper::getProductImage([$id]);
            $productImage                = CommonHelper::getListImageSrc($productImage);
            $productTag                  = CommonHelper::getProductTag([$id]);
            $productClass                = CommonHelper::getProductClass([$id]);
            $product->is_favorite        = $productFavorite;
            $product->prdoduct_tag       = $productTag;
            $product->prdoduct_class     = $productClass;
            $product->prdoduct_image     = $productImage;
            $product->category_relate    = $categoryRelate;
            $product->selection_calendar = $selectionCalendarTemp;
        }
        $result->data   = $product;
        $result->status = SDBStatusCode::OK;
        return $result;
    }

    public function getclassCategory2($request)
    {
        $result = new DataResultCollection();
        $data = SDB::table('dtb_product_class as p_class')
                    ->where('p_class.del_flg', SysConst::NOT_DEL_FLG)
                    ->where('p_class.product_id', $request->input('product_id'))
                    ->where('p_class.class_category_id1', $request->input('class_category_id'))
                    ->where(function ($query) {
                        $query->where('stock', '>', 0)
                            ->orwhere('stock_unlimited', ProductConst::STOCK_UNLIMITED);
                    })
                    ->join('dtb_class_category as c_category', 'p_class.class_category_id2', '=', 'c_category.class_category_id')
                    ->select('c_category.class_category_id', 'c_category.name')
                    ->orderBy('c_category.class_category_id')
                    ->get();
        $result->status = SDBStatusCode::OK;
        $result->data = $data;
        return $result;
    }

    public function getPriceClassProduct($request)
    {
        $result = new DataResultCollection();
        $data = SDB::table('dtb_product_class')
                    ->where('del_flg', SysConst::NOT_DEL_FLG)
                    ->where('product_id', $request->input('product_id'))
                    ->where('class_category_id1', $request->input('class_category_id1'))
                    ->where('class_category_id2', $request->input('class_category_id2'))
                    ->select('stock', 'price01', 'price02', 'sale_limit', 'stock_unlimited', 'product_class_id')
                    ->get();
        $result->data = $data;
        return $result;
    }

    public function getContentContact($request)
    {
        $result         = new DataResultCollection();
        $productCode    = $request->input('product_code', null);
        $contents       = ($productCode != null) ? "[商品コード:" . $productCode . "]画像についての問い合わせ" : "";
        $customer       = null;
        if (Auth::guard('api')->check()) {
            $customer = Auth::guard('api')->user();
        }
        $pref = SDB::table('mtb_pref')->select('id', 'name')->get();
        $result->data = [
            "contents" => $contents,
            'customer' => $customer,
            'pref'     => $pref
        ];
        $result->status = SDBStatusCode::OK;
        return $result;
    }

    public function news()
    {
        $result = new DataResultCollection();
        $data = SDB::table('dtb_news')
            ->where('del_flg', SysConst::NOT_DEL_FLG)
            ->orderBy('rank', 'DESC')
            ->select('news_id', 'news_date', 'news_title', 'news_comment', 'news_url', 'link_method')
            ->orderBy('news_id', 'ASC')
            ->paginate(Pagging::API_PER_PAGE);
        foreach ($data as &$entry) {
            if ($entry->news_url != null) {
                if (substr($entry->news_url, 0, 4) != 'http') {
                    $entry->news_url = url('/' . $entry->news_url);
                }
            }
        }
        $result->status = SDBStatusCode::OK;
        $result->data = $data->items();
        $result->current_page = $data->currentPage();
        $result->last_page = $data->lastPage();
        return $result;
    }
}
