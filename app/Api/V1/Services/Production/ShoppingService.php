<?php

namespace App\Api\V1\Services\Production;

use App\Api\V1\Services\Interfaces\ShoppingServiceInterface;
use App\Core\Common\PaymentConst;
use App\Core\Common\ProductConst;
use App\Core\Common\SDBStatusCode;
use App\Core\Common\SysConst;
use App\Core\Dao\SDB;
use App\Core\Entities\DataResultCollection;
use App\Core\Helpers\CommonHelper;
use App\Core\Helpers\PaymentHelper;
use App\Core\Helpers\ProductHelper;
use App\Core\Helpers\ShoppingHelper;
use App\Core\Jobs\SendMailOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShoppingService implements ShoppingServiceInterface
{
    public function detail($request)
    {
        $result = new DataResultCollection();
        try {
            $delivery = SDB::table('dtb_delivery as d')
                            ->join('dtb_delivery_fee as de', 'de.delivery_id', '=', 'd.delivery_id')
                            ->where('del_flg', SysConst::NOT_DEL_FLG)
                            ->orderBy('rank', 'DESC')
                            ->first();
            $products = null;
            if (!empty($request->json()->all()['order_id'])) {
                $tempOrderId = $orderId = $request->json()->all()['order_id'];
            } else {
                if (!Auth::guard('api')->check()) {
                    $request = new Request($request->json()->all()['customer']);
                }
                $requestProduct = ProductHelper::convertJsonRequest($request);
                $preOrderId     = sha1(Str::random(32));
                SDB::beginTransaction();
                $payment = SDB::table('dtb_payment')
                                ->where('del_flg', ProductConst::NOT_DELETE)
                                ->orderBy('update_date', 'ASC')
                                ->first();
                $cart = ProductService::getCart($request);
                if (!empty($cart->message)) {
                    $messageWarning = $cart->message;
                }
                if (count($cart->data) < 1) {
                    $result->message = $cart->message;
                    $result->status  = SDBStatusCode::Excep;
                    return $result;
                }
                $products                   = $cart->data;
                $dataInsertOrder            = ShoppingHelper::insertOrder($request, $products, $payment, $delivery, $preOrderId);
                $products                   = $dataInsertOrder['products'];
                $orderId                    = $dataInsertOrder['order_id'];
                $customerId                 = $dataInsertOrder['customer_id'];
                $dataSummary                = $dataInsertOrder['data_summary'];
                $dataSummary['payment_id']  = $payment->payment_id;
                $dataSummary['delivery_id'] = $delivery->delivery_id;
                $shippingId = ShoppingHelper::insertShipping($request, $customerId, $orderId, $delivery);
                ShoppingHelper::insertShipmentItem($products, $orderId, $shippingId, $requestProduct['product_class_id'], $requestProduct['delivery_date']);

                SDB::commit();
            }
            $data = ShoppingHelper::GetOrderDetail($orderId, $products, $delivery->fee);
            if (empty($tempOrderId)) {
                $data['data_summary'] = $dataSummary;
            }
            $result->status = SDBStatusCode::OK;
            if (!empty($messageWarning)) {
                $result->status  = SDBStatusCode::WARNING;
                $data['message'] = $messageWarning;
            }
            if (!empty($data['message'])) {
                $result->status  = SDBStatusCode::WARNING;
                $result->message = $data['message'];
                unset($data['message']);
            }
            $result->data = $data;
            if (!empty($data['status'])) {
                $result->status = $data['status'];
                $result->data = null;
            }
        } catch (\Exception $exception) {
            $result->status = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
            CommonHelper::CommonLog($exception->getMessage());
        }
        return $result;
    }

    public function changeAddrShopping($request)
    {
        $result = new DataResultCollection();
        $addressUpdate = [];
        if (Auth::guard('api')->check()) {
            $customer = Auth::guard('api')->user();
            $address  = SDB::table('dtb_customer_address')
                            ->where('customer_id', $customer)
                            ->where('customer_address_id', $request->input('customer_address_id', null))
                            ->where('del_flg', SysConst::NOT_DEL_FLG)
                            ->first();
            if ($address) {
                $addressUpdate = [
                    'shipping_pref'         => $address->pref,
                    'shipping_name01'       => $address->name01,
                    'shipping_name02'       => $address->name02,
                    'shipping_kana01'       => $address->kana01,
                    'shipping_kana02'       => $address->kana02,
                    'shipping_company_name' => $address->company_name,
                    'shipping_tel01'        => $address->tel01,
                    'shipping_tel02'        => $address->tel02,
                    'shipping_tel03'        => $address->tel03,
                    'shipping_fax01'        => $address->fax01,
                    'shipping_fax02'        => $address->fax02,
                    'shipping_fax03'        => $address->fax03,
                    'shipping_zip01'        => $address->zip01,
                    'shipping_zip02'        => $address->zip02,
                    'shipping_zipcode'      => $address->zipcode,
                    'shipping_addr01'       => $address->addr01,
                    'shipping_addr02'       => $address->addr02,
                    'update_date'           => CommonHelper::dateNow(),
                ];
            } else {
                $result->status  = SDBStatusCode::Excep;
                $result->message = trans('api.addr.not.exists');
                return $result;
            }
        } else {
            $addressUpdate = [
                'shipping_pref'         => $request->input('pref', null),
                'shipping_name01'       => $request->input('name01', null),
                'shipping_name02'       => $request->input('name02', null),
                'shipping_kana01'       => $request->input('kana01', null),
                'shipping_kana02'       => $request->input('kana02', null),
                'shipping_company_name' => $request->input('company_name', null),
                'shipping_tel01'        => $request->input('tel01', null),
                'shipping_tel02'        => $request->input('tel02', null),
                'shipping_tel03'        => $request->input('tel03', null),
                'shipping_fax01'        => $request->input('fax01', null),
                'shipping_fax02'        => $request->input('fax02', null),
                'shipping_fax03'        => $request->input('fax03', null),
                'shipping_zip01'        => $request->input('zip01', null),
                'shipping_zip02'        => $request->input('zip02', null),
                'shipping_zipcode'      => $request->input('zip01', null) . $request->input('zip02', null),
                'shipping_addr01'       => $request->input('addr01', null),
                'shipping_addr02'       => $request->input('addr02', null),
                'update_date'           => CommonHelper::dateNow(),
            ];
        }
        SDB::table('dtb_shipping')
            ->where('order_id', $request->input('order_id', null))
            ->where('del_flg', SysConst::NOT_DEL_FLG)
            ->update($addressUpdate);
        $result->status = SDBStatusCode::OK;
        return $result;
    }

    public function changeDeliveryShopping($request)
    {
        $result = new DataResultCollection();
        try {
            SDB::beginTransaction();
            $delivery = SDB::table('dtb_delivery as d')
                            ->join('dtb_delivery_fee as de', 'de.delivery_id', '=', 'd.delivery_id')
                            ->where('d.delivery_id', $request->input('delivery_id', null))
                            ->where('del_flg', SysConst::NOT_DEL_FLG)
                            ->first();
            $baseInfor = SDB::table('dtb_base_info')->first();
            $products = SDB::table('dtb_order_detail as od')
                            ->where('od.order_id', $request->input('order_id'))
                            ->select('psc.selected_date', 'od.product_class_id', 'od.quantity')
                            ->join('plg_selection_calender_order_detail as psc', 'psc.order_detail_id',
                                '=', 'od.order_detail_id')
                            ->get();
            $product_class_id = $stock = $delivery_date = [];
            foreach ($products as $product) {
                $product_class_id[] = $product->product_class_id;
                $stock[]            = $product->quantity;
                $delivery_date[]    = $product->selected_date;
            }
            $requestGetCart = new Request([
                                              'product_class_id' => $product_class_id,
                                              'stock'            => $stock,
                                              'delivery_date'    => $delivery_date,
                                              'is_order'         => true
                                          ]);
            $products = ProductService::getCart($requestGetCart);
            $stocks = $totalAmount = 0;
            foreach ($products->data as $index_p => $entry) {
                $entry->price            = str_replace(' ' . trans("label.unit"), '', $entry->price);
                $entry->price            = str_replace(',', '', $entry->price);
                $entry->tax              = str_replace(' ' . trans("label.unit"), '', $entry->tax);
                $entry->tax              = str_replace(',', '', $entry->tax);
                $totalAmount             += $entry->price * $entry->stock;
                $stocks                  += $entry->stock;
            }
            if($entry->discount_value > 0) {
                $dataSummary['discount'] = $entry->discount_value;
            }
            if ($baseInfor->delivery_free_quantity <= $stocks || $baseInfor->delivery_free_amount <= $totalAmount) {
                $deliveryFeeTotal = 0;
            } else {
                $deliveryFeeTotal = $delivery->fee;
            }
            $deliveryUpdate = [
                'delivery_id'            => $delivery->delivery_id,
                'fee_id'                 => $delivery->fee_id,
                'shipping_delivery_name' => $delivery->name,
                'shipping_delivery_fee'  => $delivery->fee,
                'update_date'            => CommonHelper::dateNow(),
            ];
            SDB::table('dtb_shipping')
                ->where('order_id', $request->input('order_id', null))
                ->where('del_flg', SysConst::NOT_DEL_FLG)
                ->update($deliveryUpdate);
            $result->status = SDBStatusCode::OK;
            SDB::table('dtb_order')
                ->where('order_id', $request->input('order_id', null))
                ->where('del_flg', SysConst::NOT_DEL_FLG)
                ->update([
                             'delivery_fee_total' => $deliveryFeeTotal,
                             'payment_total'      => SDB::raw('subtotal + charge - discount +' . $deliveryFeeTotal),
                             'total'              => SDB::raw('subtotal + charge - discount +' . $deliveryFeeTotal),
                         ]);
            SDB::commit();
        } catch (\Exception $exception) {
            SDB::rollBack();
            $result->status  = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
        }
        return $result;
    }

    public function changePaymentShopping($request)
    {
        $result = new DataResultCollection();
        try {
            $payment = SDB::table('dtb_payment')
                            ->where('del_flg', ProductConst::NOT_DELETE)
                            ->where('payment_id', $request->input('payment_id', null))
                            ->first();
            SDB::table('dtb_order')
                ->where('order_id', $request->input('order_id', null))
                ->update([
                             'payment_id'     => $payment->payment_id,
                             'charge'         => $payment->charge,
                             'payment_method' => $payment->payment_method,
                             'update_date'    => CommonHelper::dateNow(),
                             'payment_total'  => SDB::raw('subtotal + delivery_fee_total - discount +' .$payment->charge),
                             'total'          => SDB::raw('subtotal + delivery_fee_total - discount +' .$payment->charge)
                ]);
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $exception) {
            $result->status  = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
        }
        return $result;
    }

    public function changeInfoNonMember($request)
    {
        $result = new DataResultCollection();
        try {
            $dataUpdate = [
                'order_pref' => $request->input('pref'),
                'order_name01' => $request->input('name01'),
                'order_name02' => $request->input('name02'),
                'order_kana01' => $request->input('kana01'),
                'order_kana02' => $request->input('kana02'),
                'order_zip01' => $request->input('zip01'),
                'order_zip02' => $request->input('zip02'),
                'order_addr01' => $request->input('addr01'),
                'order_addr02' => $request->input('addr02'),
                'order_email' => $request->input('email'),
                'order_tel01' => $request->input('tel01'),
                'order_tel02' => $request->input('tel02'),
                'order_tel03' => $request->input('tel03'),
                'order_fax01' => $request->input('fax01'),
                'order_fax02' => $request->input('fax02'),
                'order_fax03' => $request->input('fax03'),
                'order_company_name' => $request->input('company_name'),

            ];
            SDB::table('dtb_order')
                ->whereNull('customer_id')
                ->whereIn('status', [7, 8])
                ->where('order_id', $request->input('order_id'))
                ->update($dataUpdate);
            $result->status = SDBStatusCode::OK;
        } catch (\Exception $exception) {
            $result->status = SDBStatusCode::Excep;
            $result->message = $exception->getMessage();
        }
        return $result;
    }

    public function getPaymentInfo()
    {
        $result = new DataResultCollection();
        $years = array();
        $tmp = range(date('Y'), date('Y') + PaymentConst::numbYear);
        foreach ($tmp as $data) {
            $years[$data] = $data;
        }
        $month = array();
        $tmp = range(PaymentConst::monthStart, PaymentConst::monthEnd);
        foreach ($tmp as $data) {
            $month[$data] = $data;
        }
        $paymentConfig = PaymentHelper::getPaymentConfig();
        $tokenJsUrl = $paymentConfig->creditConnectionPlace6;
        $tokenNinsyoCode = $paymentConfig->tokenNinsyoCode;
        $payKbnKaisu = $paymentConfig->payKbnKaisu;
        $tokenUrl = "$tokenJsUrl?k_TokenNinsyoCode=$tokenNinsyoCode";
        $arrPayMethod = PaymentConst::arrPayKbnKaisu;
        $arrPayType = array();
        foreach ($payKbnKaisu as $value) {
            $arrPayType[sprintf("%02d", $value)] = $arrPayMethod[sprintf("%02d", $value)];
        }
        $result->pay_type = $arrPayType;
        $result->year = $years;
        $result->month = $month;
        $result->tokenUrl = $tokenUrl;
        $result->status = SDBStatusCode::OK;
        return $result;
    }

    public function payment($request)
    {
        /*
         * dtb_payment: Lấy ra payment có del_flg = 0, first và sort by rank
         * dtb_payment_option : Liên kết với dtb_payment theo payment_id
         * dtb_order : Lấy ra thông tin tổng tiền, phí
         * plg_sln_plugin_config: chứa thông tin config card của member
         * plg_sln_order_payment_status
         * plg_sln_mem_card_id
         *
         * */
        /*
         * Truyền vào order id, lấy trong DB ra thông tin order, nếu không có thì redirect màn hình shopping_error và
         * báo lỗi :受注情報は存在していません。
         * Month view default 12, numb year lấy year hiện tại cộng thêm 10 => dropdown
         * Regex year: /^[0-9]+$/
         * Payment type lấy trong bảng plg_sln_plugin_config sub_data và lấy ra payKbnKaisu và join với constant
         * arrray option
         * Config payment lấy trong bảng plg_sln_plugin_config theo id desc
         * */
        $result = new DataResultCollection();
        return $result;
    }

    public function calculateDeliveryFee(Order $Order)
    {

        // 配送業者を取得
        $shippings = $Order->getShippings();

        // 配送料合計金額
        $Order->setDeliveryFeeTotal($this->getShippingDeliveryFeeTotal($shippings));

        // 配送料無料条件(合計金額)
        $this->setDeliveryFreeAmount($Order);

        // 配送料無料条件(合計数量)
        $this->setDeliveryFreeQuantity($Order);

        return $Order;

    }

    public function order($request)
    {
        $result = new DataResultCollection();
        $products = SDB::table('dtb_order_detail as od')
            ->where('od.order_id', $request->input('order_id'))
            ->select('psc.selected_date', 'od.product_class_id', 'od.quantity', 'od.product_name'
                , 'od.class_category_name1', 'od.class_category_name2', 'od.price', 'od.tax_rate'
                , 'od.tax_rule')
            ->join('plg_selection_calender_order_detail as psc', 'psc.order_detail_id',
                '=', 'od.order_detail_id')
            ->get();
        $product_class_id = $stock = $delivery_date = [];
        foreach ($products as $product) {
            $product_class_id[] = $product->product_class_id;
            $stock[] = $product->quantity;
            $delivery_date[] = $product->selected_date;
        }

        $requestGetCart = new Request([
            'product_class_id' => $product_class_id,
            'stock' => $stock,
            'delivery_date' => $delivery_date,
            'is_order' => true
        ]);
        $products = ProductService::getCart($requestGetCart);
        if ($products->message == null) {
            if ($request->input('delivery_time') != null && $request->input('delivery_date') != null) {
                $dataUpdate = [
                    'shipping_delivery_time' => $request->input('delivery_time'),
                    'shipping_delivery_date' => $request->input('delivery_date'),
                ];
                SDB::table('dtb_shipping')
                    ->where('order_id', $request->input('order_id'))
                    ->update($dataUpdate);
            }
            SDB::beginTransaction();
            try {
                $order = SDB::table('dtb_order as o')
                    ->where('o.order_id', $request->input('order_id'))
                    ->whereIn('o.status', [7, 8])
                    ->leftjoin('dtb_shipping as sh', 'o.order_id', '=', 'sh.order_id')
                    ->leftjoin('mtb_pref as p', 'p.id', '=', 'o.order_pref')
                    ->join('dtb_order_detail as od', 'od.order_id', '=', 'o.order_id')
                    ->select('o.order_id as id', 'o.payment_total', 'o.payment_method'
                        , 'o.message', 'o.subtotal', 'o.tax', 'o.charge', 'o.delivery_fee_total', 'o.discount'
                        , 'o.order_name01 as name01', 'o.order_name02 as name02', 'o.order_kana01 as kana01',
                        'o.order_kana02 as kana02', 'o.order_company_name as company_name', 'o.order_zipcode as zipcode'
                        , 'o.order_zip01 as zip01', 'o.order_zip02 as zip02', 'o.order_addr01 as addr01', 'o.order_addr02 as addr02'
                        , 'o.order_tel01 as tel01', 'o.order_tel02 as tel02', 'o.order_tel03 as tel03', 'o.order_fax01 as fax01'
                        , 'o.order_fax02 as fax02', 'o.order_fax03 as fax03', 'o.order_email as email'
                        , 'od.tax_rate', 'od.tax_rule', 'od.product_code', 'od.product_name', 'od.price', 'od.class_category_name1'
                        , 'od.class_category_name2','o.payment_id','p.name as pref_name','od.quantity')
                    ->get();
                if(count($order) > 0) {
                    $paymentConfig = SDB::table('plg_sln_plugin_config')
                        ->select('sub_data')
                        ->orderBy('id', 'DESC')
                        ->first();
                    $shippings = SDB::table('dtb_shipping as s')
                        ->join('dtb_shipment_item as si', 'si.shipping_id', '=', 's.shipping_id')
                        ->leftjoin('mtb_pref as p', 'p.id', '=', 's.shipping_pref')
                        ->where('s.order_id', $request->input('order_id'))
                        ->where('del_flg', SysConst::NOT_DEL_FLG)
                        ->select('s.shipping_zipcode as zipcode','s.shipping_name01 as name01', 's.shipping_name02 as name02', 's.shipping_kana01 as kana01'
                            , 's.shipping_kana02 as kana02', 's.shipping_company_name as company_name'
                            , 's.shipping_zip01 as zip01', 's.shipping_zip02 as zip02', 'p.name as pref_name', 's.shipping_addr01 as addr01'
                            , 's.shipping_addr02 as addr02', 's.shipping_tel01 as tel01', 's.shipping_tel02 as tel02'
                            , 's.shipping_tel03 as tel03', 's.shipping_fax01 as fax01', 's.shipping_fax02 as fax02'
                            , 's.shipping_fax03 as fax03', 's.shipping_delivery_date', 's.shipping_delivery_time'
                            , 'si.product_code', 'si.product_name', 'si.class_category_name1','si.class_category_name2', 'si.quantity')
                        ->get();
                    $payId = $order[0]->payment_id;
                    if (!empty(json_decode($paymentConfig->sub_data)->eccubePaymentIds->$payId)) {
                        $result->data = [
                            'credit_card' => true
                        ];
                    } else {
                        if (Auth::guard('api')->check()) {
                            ShoppingHelper::customerUpdate($order[0]);
                        }
                        ShoppingHelper::orderUpdate($request->input('order_id'));
                        ShoppingHelper::stockUpdate($products->data);
                        SendMailOrder::dispatch($order, $shippings);
                    }
                }
                SDB::commit();
            } catch (\Exception $e) {
                SDB::rollBack();
                CommonHelper::CommonLog($e->getMessage());
                $result->status = SDBStatusCode::Excep;
                $result->message = $e->getMessage();
            }
        }
        $result->status = $products->status;
        $result->message = $products->message;
        return $result;

    }


}
