<?php

namespace App\Api\V1\Services\Production;

use App\Api\V1\Services\Interfaces\UserServiceInterface;
use App\Core\Common\Pagging;
use App\Core\Common\ProductConst;
use App\Core\Common\SDBStatusCode;
use App\Core\Common\SysConst;
use App\Core\Common\UserConst;
use App\Core\Dao\SDB;
use App\Core\Entities\DataResultCollection;
use App\Core\Helpers\AuthHelper;
use App\Core\Helpers\ShoppingHelper;
use App\Core\Helpers\TaxHelper;
use App\Core\Helpers\CommonHelper;
use App\Core\Jobs\SendMailForgotPassword;
use App\Core\Jobs\SendMailRegisterNormal;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;

class UserService implements UserServiceInterface
{
    public function userInfo($userId)
    {
        $result = new DataResultCollection();
        try {
            $user = SDB::table('users')->leftJoin('sys_roles as role', 'users.role_value', '=', 'role.role_value')->leftJoin('users_detail as detail', 'users.id', '=', 'detail.user_id')->where('users.id', $userId)->select('users.id', 'users.email', 'role.name as role_name', 'detail.*')->first();
            if (!empty($user)) {
                $user->src = CommonHelper::getAvatar($user->avatar);
            }
            $result->data = $user;
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $exception) {
            $result->status = SDBStatusCode::ApiError;
            $result->message = $exception->getMessage();
            CommonHelper::logException($exception);
        }
        return $result;
    }

    public function registerNormal($request)
    {
        $result = new DataResultCollection();
        try {
            SDB::beginTransaction();
            $salt = AuthHelper::createSalt(5);
            $pass = AuthHelper::encodePassword($request->input('password'), $salt);
            $secretKey = AuthHelper::getUniqueSecretKey();
            $dataUser = array(
                'status' => UserConst::TEMPORARY_MEMBER,
                'sex' => $request->input('sex', null),
                'job' => $request->input('job', null),
                'country_id' => $request->input('country_id', null),
                'birth' => $request->input('birth', null),
                'pref' => $request->input('pref'),
                'name01' => $request->input('name01'),
                'name02' => $request->input('name02'),
                'kana01' => $request->input('kana01'),
                'kana02' => $request->input('kana02'),
                'company_name' => $request->input('company_name', null),
                'zip01' => $request->input('zip01'),
                'zip02' => $request->input('zip02'),
                'zipcode' => $request->input('zip01') . $request->input('zip02'),
                'addr01' => $request->input('addr01'),
                'addr02' => $request->input('addr02'),
                'email' => $request->input('email'),
                'tel01' => $request->input('tel01'),
                'tel02' => $request->input('tel02'),
                'tel03' => $request->input('tel03'),
                'fax01' => $request->input('fax01', null),
                'fax02' => $request->input('fax02', null),
                'fax03' => $request->input('fax03', null),
                'salt' => $salt,
                'password' => $pass,
                'secret_key' => $secretKey,
                'del_flg' => UserConst::ENABLED,
                'create_date' => CommonHelper::dateNow(),
                'update_date' => CommonHelper::dateNow(),
            );
            $newUserId = SDB::table('dtb_customer')->insertGetId($dataUser);
            $dataUserDetail = array(
                'customer_id' => $newUserId,
                'country_id' => $request->input('country_id', null),
                'pref' => $request->input('pref'),
                'name01' => $request->input('name01'),
                'name02' => $request->input('name02'),
                'kana01' => $request->input('kana01'),
                'kana02' => $request->input('kana02'),
                'company_name' => $request->input('company_name', null),
                'zip01' => $request->input('zip01'),
                'zip02' => $request->input('zip02'),
                'zipcode' => $request->input('zip01') . $request->input('zip02'),
                'addr01' => $request->input('addr01'),
                'addr02' => $request->input('addr02'),
                'tel01' => $request->input('tel01'),
                'tel02' => $request->input('tel02'),
                'tel03' => $request->input('tel03'),
                'fax01' => $request->input('fax01', null),
                'fax02' => $request->input('fax02', null),
                'fax03' => $request->input('fax03', null),
                'del_flg' => UserConst::ENABLED,
                'create_date' => CommonHelper::dateNow(),
                'update_date' => CommonHelper::dateNow(),
            );
            SDB::table('dtb_customer_address')->insert($dataUserDetail);
            $linkActive = env('URL_BASE') . 'entry/activate/' . $secretKey;
            $body = [
                "dynamicLinkInfo" => [
                    "domainUriPrefix" => env("FIREBASE_DOMAIN_UriPrefix"),
                    "link" => $linkActive,
                    "androidInfo" => [
                        "androidPackageName" => env("FIREBASE_BUNDLE_ANDROID")
                    ],
                    "iosInfo" => [
                        "iosBundleId" => env("FIREBASE_BUNDLE_IOS")
                    ]
                ]
            ];
            $response = Curl::to(env("FIREBASE_LINK"))->withData($body)->asJson(true)->post();
            $shortLink = $response['shortLink'];
            SendMailRegisterNormal::dispatch($request->all(), $shortLink);
            $result->status = SDBStatusCode::OK;
            SDB::commit();
        } catch (\Exception $exception) {
            SDB::rollBack();
            $result->status = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
        }
        return $result;
    }

    public function removeDeviceToken($userId, $deviceId)
    {
        $result = new DataResultCollection();
        try {
            $tableName = 'app_user_device_alive';
            $isExists = SDB::table($tableName)->where('device_id', $deviceId)->where('user_id', $userId)->exists();
            if ($isExists == true) {
                SDB::table($tableName)->where('device_id', $deviceId)->where('user_id', $userId)->delete();
            }
            $result->status = SDBStatusCode::OK;

        } catch (\Exception $e) {
            $result->message = $e->getMessage();
            $result->status = SDBStatusCode::Excep;
            CommonHelper::logException($e);
        }
        return $result;
    }

    public function forgotPassword(array $inputs)
    {
        $result = new DataResultCollection();
        $user = SDB::table('dtb_customer')->where('email', $inputs['email'])->exists();
        if (!$user) {
            $result->status = SDBStatusCode::Excep;
            $result->message = trans('api.user.not.exists');
            return $result;
        }
        try {
            $resetKey = AuthHelper::getUniqueResetKey();
            $dataUpdate = [
                'reset_key' => $resetKey,
                'reset_expire' => CommonHelper::dateExpite(env('CUSTOMER_RESET_EXPIRE', 600))
            ];
            SDB::table('dtb_customer')->where('email', $inputs['email'])->update($dataUpdate);
            SendMailForgotPassword::dispatch($inputs, $resetKey);
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $exception) {
            CommonHelper::CommonLog($exception);
            $result->status = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
        }
        return $result;
    }

    public function userUpdate($request)
    {
        $result = new DataResultCollection();
        try {
            $user = Auth::user();
            $dataUser = array(
                'sex' => $request->input('sex', null),
                'job' => $request->input('job', null),
                'country_id' => $request->input('country_id', null),
                'pref' => $request->input('pref'),
                'name01' => $request->input('name01'),
                'name02' => $request->input('name02'),
                'kana01' => $request->input('kana01'),
                'kana02' => $request->input('kana02'),
                'company_name' => $request->input('company_name', null),
                'zip01' => $request->input('zip01'),
                'zip02' => $request->input('zip02'),
                'zipcode' => $request->input('zip01') . $request->input('zip02'),
                'addr01' => $request->input('addr01'),
                'addr02' => $request->input('addr02'),
                'email' => $request->input('email'),
                'tel01' => $request->input('tel01'),
                'tel02' => $request->input('tel02'),
                'tel03' => $request->input('tel03'),
                'fax01' => $request->input('fax01', null),
                'fax02' => $request->input('fax02', null),
                'fax03' => $request->input('fax03', null),
                'birth' => $request->input('birth', null)
            );
            if ($request->input('password') != UserConst::PASSWORD_DEFAULT) {
                if (empty($user->salt)) {
                    $salt = AuthHelper::createSalt(5);
                    $dataUser['salt'] = $salt;
                } else $salt = $user->salt;
                $password = AuthHelper::encodePassword($request->input('password'), $salt);
                $dataUser['password'] = $password;
            }
            SDB::table('dtb_customer')
                ->where('customer_id', $user->customer_id)
                ->whereNot('status', UserConst::notActive)
                ->where('del_flg', SysConst::NOT_DEL_FLG)
                ->update($dataUser);
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $exception) {
            $result->status = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
            CommonHelper::logException($exception);
        }
        return $result;
    }

    public function deleteUser()
    {
        $result = new DataResultCollection();
        $dataUpdate = array(
            'del_flg' => UserConst::DISABLED,
            'email' => Str::random(60) . '@dummy.dummy'
        );
        $userId = Auth::id();
        SDB::table('oauth_access_tokens')->where('user_id', $userId)->update([
            'revoked' => UserConst::DISABLED,
            'updated_at' => CommonHelper::dateNow()
        ]);

        SDB::table('dtb_customer')->where('customer_id', $userId)->update($dataUpdate);
        $result->status = SDBStatusCode::OK;
        return $result;
    }

    public function deleteProductFavorite($id)
    {
        $result = new DataResultCollection();
        try {
            SDB::table('dtb_customer_favorite_product')
                ->where('product_id', $id)
                ->where('customer_id', Auth::id())
                ->update(['del_flg' => SysConst::DEL_FLG]);
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $exception) {
            $result->status = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
        }
        return $result;
    }

    public function orderHistory($request)
    {
        $result = new DataResultCollection();
        $querySubImage = SDB::table("dtb_product_image")->groupBy("product_id")->selectRaw("product_id,min(dtb_product_image.rank) as i_rank");
        $arrProductImage = SDB::table("dtb_product_image as i")->joinSub($querySubImage, "image_group", function ($join) {
            $join->on("i.product_id", "=", "image_group.product_id");
            $join->on("i.rank", "=", "image_group.i_rank");
        })->select("i.file_name", "i.product_id");
        $order = SDB::table('dtb_order as o')->where('o.customer_id', Auth::id())->where('o.del_flg', UserConst::ENABLED)->whereNotIn('o.status', [
            7,
            8
        ])->join('dtb_order_detail as od', function ($join) {
            $join->on('o.order_id', 'od.order_id');
        })->leftjoinsub($arrProductImage, "i", function ($join) {
            $join->on("i.product_id", "od.product_id");
        })->join('mtb_customer_order_status as os', function ($join) {
            $join->on('o.status', 'os.id');
        })->join('plg_selection_calender_order_detail as select_c', function ($query) {
            $query->on('select_c.order_detail_id', 'od.order_detail_id');
            $query->on('select_c.product_class_id', 'od.product_class_id');
        })->select('od.*', 'o.customer_id', 'o.update_date', 'o.status', 'o.subtotal', 'o.payment_total', 'i.file_name', 'o.charge', 'o.discount', 'o.delivery_fee_total', 'os.name as status_name', 'select_c.selected_date', 'o.create_date')->orderBy('update_date', 'desc')->paginate(Pagging::API_PER_PAGE);
        $currentPage = $order->currentPage();
        $lastPage = $order->lastPage();
        $arrFieldProduct = [
            'order_id',
            'payment_total',
            'charge',
            'delivery_fee_total',
            'discount',
            'status_name',
            'create_date'
        ];
        $arrFieldOrder = [
            'product_name',
            'file_name',
            'product_id',
            'order_detail_id',
            'price',
            'product_code',
            'quantity',
            'selected_date'
        ];
        $order = $order->groupBy('order_id');
        $dataProduct = [];
        foreach ($order as $entry) {
            $dataResult = new \stdClass();
            $products = [];
            foreach ($entry as $key => $value) {
                $value->file_name = CommonHelper::getImageSrc($value->file_name);
                $product = new \stdClass();
                if ($key == 0) {
                    foreach ($arrFieldProduct as $valueProduct) {
                        if (in_array($valueProduct, [
                            'payment_total',
                            'charge',
                            'delivery_fee_total',
                            'discount'
                        ])) {
                            $dataResult->$valueProduct = CommonHelper::getProductPrice((int)$value->$valueProduct);
                        } else {
                            $dataResult->$valueProduct = $value->$valueProduct;
                        }
                    }
                }
                foreach ($arrFieldOrder as $valueOrder) {
                    if ($valueOrder == 'price') {
                        $product->$valueOrder = CommonHelper::getProductPrice($value->$valueOrder +
                            TaxHelper::calcTax($value->$valueOrder, $value->tax_rate, $value->tax_rule));
                    } else {
                        $product->$valueOrder = $value->$valueOrder;
                    }
                }
                $products[] = $product;
            }
            $dataResult->products = $products;
            $dataProduct[] = $dataResult;
        }
        $result->data = $dataProduct;
        $result->current_page = $currentPage;
        $result->last_page = $lastPage;
        $result->status = SDBStatusCode::OK;

        return $result;
    }

    public function orderDetail($id)
    {
        $result = new DataResultCollection();
        $result->status = SDBStatusCode::OK;
        $querySubImage = SDB::table("dtb_product_image")->groupBy("product_id")->selectRaw("product_id,min(dtb_product_image.rank) as i_rank");
        $arrProductImage = SDB::table("dtb_product_image as i")->joinSub($querySubImage, "image_group", function ($join) {
            $join->on("i.product_id", "=", "image_group.product_id");
            $join->on("i.rank", "=", "image_group.i_rank");
        })->select("i.file_name", "i.product_id");
        $order = SDB::table('dtb_order as o')->where('o.customer_id', Auth::id())->where('o.del_flg', UserConst::ENABLED)->where('o.order_id', $id)->whereNotIn('o.status', [
            7,
            8
        ])->join('dtb_order_detail as od', function ($join) {
            $join->on('o.order_id', 'od.order_id');
        })->leftjoinsub($arrProductImage, "i", function ($join) {
            $join->on("i.product_id", "od.product_id");
        })->leftjoin('mtb_customer_order_status as os', function ($join) {
            $join->on('o.status', 'os.id');
        })->leftjoin('dtb_shipping as sh', function ($join) {
            $join->on('o.order_id', 'sh.order_id');
        })->leftjoin('mtb_pref as pr', function ($query) {
            $query->on('pr.id', 'sh.shipping_pref');
        })->leftjoin('dtb_mail_history as m_his', function ($query) {
            $query->on('m_his.order_id', 'o.order_id');
        })->join('plg_selection_calender_order_detail as select_c', function ($query) {
            $query->on('select_c.order_detail_id', 'od.order_detail_id');
            $query->on('select_c.product_class_id', 'od.product_class_id');
        })->select('od.*', 'o.customer_id', 'o.update_date', 'o.payment_method', 'o.status', 'o.subtotal', 'o.payment_total', 'i.file_name', 'o.charge', 'o.discount', 'o.delivery_fee_total', 'os.name as status_name', 'pr.name as pref_name', 'sh.shipping_name01 as name01', 'sh.shipping_name02 as name02', 'sh.shipping_kana01 as kana01', 'sh.shipping_kana02 as kana02', 'sh.shipping_tel01 as tel01', 'sh.shipping_tel02 as tel02', 'sh.shipping_tel03 as tel03', 'sh.shipping_zip01 as zip01', 'sh.shipping_zip02 as zip02', 'sh.shipping_addr01 as addr01', 'sh.shipping_addr02 as addr02', 'sh.shipping_delivery_name as delivery_name', 'sh.shipping_delivery_time as delivery_time', 'sh.shipping_delivery_date as delivery_date', 'select_c.selected_date')->orderBy('update_date', 'desc')->get();
        if (count($order) > 0) {
            $arrFieldProduct = [
                'order_id',
                'payment_total',
                'charge',
                'delivery_fee_total',
                'discount',
                'status_name',
                'delivery_name',
                'delivery_time',
                'delivery_date',
                'update_date',
                'payment_method'
            ];
            $arrFieldOrder = [
                'product_id',
                'order_detail_id',
                'price',
                'product_code',
                'quantity',
                'file_name',
                'selected_date',
                'product_name'
            ];
            $arrFieldAddr = [
                'pref_name',
                'name01',
                'name02',
                'kana01',
                'kana02',
                'tel01',
                'tel02',
                'tel03',
                'zip01',
                'zip02',
                'addr01',
                'addr02'
            ];
            $dataResult = new \stdClass();
            $addrTemp = new \stdClass();
            $products = [];
            foreach ($order as $key => $value) {
                $value->file_name = CommonHelper::getImageSrc($value->file_name);
                $product = new \stdClass();
                if ($key == 0) {
                    foreach ($arrFieldProduct as $valueProduct) {
                        if (in_array($valueProduct, [
                            'payment_total',
                            'charge',
                            'delivery_fee_total',
                            'discount'
                        ])) {
                            $dataResult->$valueProduct = CommonHelper::getProductPrice((int)$value->$valueProduct);
                        } else {
                            $dataResult->$valueProduct = $value->$valueProduct;
                        }
                    }
                    $dataResult->inquiry = null;
                    foreach ($arrFieldAddr as $entry) {
                        $addrTemp->$entry = $value->$entry;
                    }
                }
                foreach ($arrFieldOrder as $valueOrder) {
                    if ($valueOrder == 'price') {
                        $product->$valueOrder = CommonHelper::getProductPrice($value->$valueOrder +
                            TaxHelper::calcTax($value->$valueOrder, $value->tax_rate, $value->tax_rule));
                    } else {
                        $product->$valueOrder = $value->$valueOrder;
                    }
                }
                $products[] = $product;
            }
            $dataResult->products = $products;
            $address = ShoppingHelper::formatAddress($addrTemp);
            $dataResult->addr = $address;
            $result->data = $dataResult;
        }
        return $result;
    }

    public function getPref()
    {
        $result = new DataResultCollection();
        $result->data = SDB::table('mtb_pref')->get();
        $result->status = SDBStatusCode::OK;
        return $result;
    }

    public function getJob()
    {
        $result = new DataResultCollection();
        $result->data = SDB::table('mtb_job')->get();
        $result->status = SDBStatusCode::OK;
        return $result;
    }

    public function getPrefAndJob()
    {
        $result = new DataResultCollection();
        $pref = SDB::table('mtb_pref')->get();
        $job = SDB::table('mtb_job')->get();
        $data = new \stdClass();
        $data->pref = $pref;
        $data->job = $job;
        $result->status = SDBStatusCode::OK;
        $result->data = $data;
        return $result;
    }

    public function GetAddressByPostcode($request)
    {
        $postcode = $request->zip01 . $request->zip02;
        $result = new DataResultCollection();
        try {
            $filePostcode = file_get_contents('https://yubinbango.github.io/yubinbango-data/data/' .
                $request->zip01 . '.js', false);
            if ($filePostcode) {
                $filePostcode = json_decode(substr($filePostcode, 7, strlen($filePostcode) - 10));
                if (!empty($filePostcode->$postcode)) {
                    $data = new \stdClass();
                    $data->pref = $filePostcode->$postcode[0];
                    $data->addr01 = $filePostcode->$postcode[2] . $filePostcode->$postcode[1];
                    $data->addr02 = isset($filePostcode->$postcode[3]) ? $filePostcode->$postcode[3] : '';
                    $result->status = SDBStatusCode::OK;
                    $result->data = $data;
                } else {
                    $result->status = SDBStatusCode::Excep;
                    $result->message = trans('api.POSTCODE_NOT_EXISTS');
                }
            }
        } catch (\Exception $e) {
            $result->status = SDBStatusCode::Excep;
            $result->message = $e->getMessage();
        }
        return $result;
    }

    public function getListFavorite($request)
    {
        $result = new DataResultCollection();
        $idsProductFavorite = SDB::table('dtb_customer_favorite_product as cfp')
            ->where('customer_id', Auth::id())
            ->where('cfp.del_flg', UserConst::ENABLED)
            ->join('dtb_product as p', 'cfp.product_id', '=', 'p.product_id')
            ->orderBy('cfp.update_date', 'DESC')
            ->pluck('cfp.product_id')
            ->toArray();
        $arrProductImage = ProductHelper::queryGetImage();
        $arrProduct = SDB::table("dtb_product as p")
            ->whereIn('p.product_id', $idsProductFavorite)
            ->leftJoinSub($arrProductImage, "i", function ($join) {
                $join->on("i.product_id", "p.product_id");
            })
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
            ->groupBy("p.product_id", "p.name", "p.create_date", "i.file_name")
            ->selectRaw("p.product_id,p.name,i.file_name,min(p_class.price02) as price_from,max(p_class.price02) as price_to")
            ->paginate(Pagging::API_PER_PAGE);
        foreach ($arrProduct as &$entry) {
            $entry->price = CommonHelper::getProductPrice($entry->price_from, $entry->price_to);
            $entry->file_name = CommonHelper::getImageSrc($entry->file_name);
            unset($entry->price_from);
            unset($entry->price_to);
        }
        $result->data = $arrProduct->items();
        $result->current_page = $arrProduct->currentPage();
        $result->last_page = $arrProduct->lastPage();
        $result->status = SDBStatusCode::OK;
        return $result;
    }

    public function mailHistory($orderId)
    {
        $result = new DataResultCollection();
        $uId = Auth::id();
        $mails = SDB::table('dtb_order as od')
            ->where('customer_id', $uId)
            ->where('od.order_id', $orderId)
            ->join('dtb_mail_history as mail', 'od.order_id', '=', 'mail.order_id')
            ->select('mail.send_id', 'mail.subject', 'mail.send_date')
            ->get();
        $result->status = SDBStatusCode::OK;
        $result->data = $mails;
        return $result;
    }

    public function mailDetail($id)
    {
        $result = new DataResultCollection();
        $uId = Auth::id();
        $mail = SDB::table('dtb_order as od')
            ->where('customer_id', $uId)
            ->where('mail.send_id', $id)
            ->join('dtb_mail_history as mail', 'od.order_id', '=', 'mail.order_id')
            ->select('mail.send_id', 'mail.order_id', 'mail.subject', 'mail.send_date', 'mail.mail_body')->first();
        $result->status = SDBStatusCode::OK;
        $result->data = $mail;
        return $result;
    }
}
