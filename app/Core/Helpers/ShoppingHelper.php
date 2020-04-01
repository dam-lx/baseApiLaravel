<?php

namespace App\Core\Helpers;

use App\Api\V1\Services\Production\ProductService;
use App\Core\Common\CalenderConst;
use App\Core\Common\ProductConst;
use App\Core\Common\SDBStatusCode;
use App\Core\Common\SysConst;
use App\Core\Dao\SDB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShoppingHelper
{

    public static function getOrder($orderId = null, $products = null)
    {
        if ($products == null) {
            $products = SDB::table('dtb_order_detail as od')
                ->where('od.order_id', $orderId)
                ->select('psc.selected_date', 'od.product_class_id', 'od.quantity')
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
            $data = [
                'status' => $products->status,
                'data' => $products->data,
                'message' => $products->message
            ];
        } else {
            $data = [
                'data' => $products
            ];
        }
        return $data;
    }

    public static function formatAddress($addr)
    {
        if ($addr) {
            $address = [
                'zip_display' => '〒' . $addr->zip01 . '-' . $addr->zip02 . ' ' . $addr->pref_name . $addr->addr01 .
                                 $addr->addr02,
                'tel_display' => $addr->tel01 . '-' . $addr->tel02 . '-' . $addr->tel03,
                'zip01'       => $addr->zip01,
                'zip02'       => $addr->zip02,
                'tel01'       => $addr->tel01,
                'tel02'       => $addr->tel02,
                'tel03'       => $addr->tel03,
                'name01'      => $addr->name01,
                'name02'      => $addr->name02,
            ];
            if (!empty($addr->kana01)) {
                $address['name_display'] = $addr->name01 . '(' . $addr->kana01 . $addr->kana02 . ')' . $addr->name02 . '様';
            } else {
                $address['name_display'] = $addr->name01 . $addr->name02 . '様';
            }
            if (!empty($addr->email)) {
                $address['email'] = $addr->email;
            }
            if (!empty($addr->customer_address_id)) {
                $address['customer_address_id'] = $addr->customer_address_id;
            }
            if (!empty($addr->company_name)) {
                $address['company_name'] = $addr->company_name;
            }
        } else $address = null;
        return $address;
    }

    public static function getAddress($orderId)
    {
        $shipping = SDB::table('dtb_order as od')
            ->where('od.order_id', $orderId)
            ->where('od.del_flg', SysConst::NOT_DEL_FLG)
            ->join('mtb_pref as p', 'p.id', '=', 'od.order_pref')
            ->select('od.order_name01 as name01', 'od.order_name02 as name02', 'od.order_zip01 as zip01',
                'od.order_zip02 as zip02', 'od.order_addr01 as addr01', 'od.order_addr02 as addr02',
                'od.order_tel01 as tel01', 'od.order_tel02 as tel02', 'od.order_tel03 as tel03', 'od.order_company_name as company_name',
                'p.name as pref_name', 'od.order_email as email')
            ->first();
        return self::formatAddress($shipping);
    }

    public static function getAddrCustomer($orderId)
    {
        $shipping = SDB::table('dtb_shipping as s')
            ->where('s.order_id', $orderId)
            ->where('s.del_flg', SysConst::NOT_DEL_FLG)
            ->join('mtb_pref as p', 'p.id', '=', 's.shipping_pref')
            ->join('dtb_order as od', 'od.order_id', '=', 's.order_id')
            ->select('s.shipping_name01 as name01', 's.shipping_name02 as name02', 's.shipping_zip01 as zip01',
                's.shipping_zip02 as zip02', 's.shipping_addr01 as addr01', 's.shipping_addr02 as addr02',
                's.shipping_tel01 as tel01', 's.shipping_tel02 as tel02', 's.shipping_tel03 as tel03', 's.shipping_company_name as company_name',
                'p.name as pref_name', 'od.order_email as email')
            ->first();
        return self::formatAddress($shipping);
    }

    public static function getListDelivery()
    {
        return SDB::table('dtb_delivery')
            ->where('del_flg', SysConst::NOT_DEL_FLG)
            ->select('name', 'delivery_id')
            ->orderBy('rank', 'ASC')
            ->get();
    }

    public static function getListPayment($deliveryId)
    {
        return SDB::table('dtb_payment_option as po')
            ->where('delivery_id', $deliveryId)
            ->join('dtb_payment as p', 'po.payment_id', '=', 'p.payment_id')
            ->select('p.payment_method', 'p.payment_id', 'p.charge', 'p.payment_id')
            ->get();
    }

    public static function getListDeliveryTime($deliveryId)
    {
        return SDB::table('dtb_delivery_time')
            ->where('delivery_id', $deliveryId)
            ->select('time_id', 'delivery_time')
            ->get();
    }

    public static function getDeliveryDate($orderId)
    {
        return SDB::table('dtb_order_detail as od')
            ->where('order_id', $orderId)
            ->join('dtb_product_class as p_class', 'p_class.product_class_id', '=', 'od.product_class_id')
            ->join('dtb_delivery_date as d_date', 'd_date.date_id', '=', 'p_class.delivery_date_id')
            ->selectRaw("max(d_date.value) as delivery_date")
            ->first();
    }

    public static function GetOrderDetail($orderId, $productsCart = null, $deliveryFee = null, $checkOrder = null)
    {
        $products = self::getOrder($orderId, $productsCart);
        if (!empty($products['status']) && $products['status'] == SDBStatusCode::Excep
            || (!empty($checkOrder) && !empty($products['status']) && $products['status'] != SDBStatusCode::OK)) {
            $data = [
                'status' => $products['status'],
                'message' => $products['message']
            ];
            return $data;
        }
        $shipping = SDB::table('dtb_shipping as sh')
            ->where('sh.order_id', $orderId)
            ->where('sh.del_flg', SysConst::NOT_DEL_FLG)
            ->join('dtb_order as od', 'od.order_id', '=', 'sh.order_id')
            ->select('sh.delivery_id', 'od.payment_id', 'od.payment_total','od.subtotal')
            ->first();
        if ($shipping) {
            $dataSummary = [
                'delivery_id' => $shipping->delivery_id,
                'payment_id' => $shipping->payment_id,
                'sub_total' => $shipping->subtotal,
                'total_price'=> $shipping->payment_total
            ];
            $address = self::getAddress($orderId);
            $delivery = self::getListDelivery();
            $payment = self::getListPayment($shipping->delivery_id);
            $deliveryTime = self::getListDeliveryTime($shipping->delivery_id);
            $deliveryDate = self::getDeliveryDate($orderId);
            $deliveryDates = [
                'from' => CommonHelper::timeExpiteToDate($deliveryDate->delivery_date * 86400),
                'to' => CommonHelper::timeExpiteToDate(($deliveryDate->delivery_date + env('DELIVERY_DATE_END_MAX')) * 86400)
            ];
            if ($productsCart == null) {
                $stocks = $totalAmount = $totalTax = 0;
                foreach ($products['data'] as $index_p => $entry) {
                    $totalAmount += $entry->price * $entry->stock;
                    $totalTax += $entry->tax;
                    $stocks += $entry->stock;
                    $dataOrderDetail = [
                        'price' => $entry->price,
                        'tax_rate' => $entry->tax_rate,
                        'tax_rule' => $entry->tax_rule
                    ];
                    SDB::table('dtb_order_detail')
                        ->where('product_class_id', $entry->product_class_id)
                        ->where('order_id', $orderId)
                        ->update($dataOrderDetail);
                    $dataShipping = [
                        'price' => $entry->price,
                    ];
                    SDB::table('dtb_shipment_item')
                        ->where('product_class_id', $entry->product_class_id)
                        ->where('order_id', $orderId)
                        ->update($dataShipping);
                    $remove = ['price02', 'tax', 'tax_rule', 'tax_rate', 'discount_rate'];
                    $products['data'][$index_p] = CommonHelper::unset_multikey($entry, $remove);
                }

                $baseInfor = SDB::table('dtb_base_info')->first();
                if ($baseInfor->delivery_free_quantity <= $stocks || $baseInfor->delivery_free_amount <= $totalAmount) {
                    $deliveryFee = 0;
                }
                $dataSummary['shipping_price'] = $deliveryFee;
                $dataOrder = [
                    'subtotal' => $totalAmount,
                    'tax' => $totalTax,
                    'total' => $totalAmount - $entry->discount_value,
                    'payment_total' => $totalAmount - $entry->discount_value,
                    'discount' => $entry->discount_value
                ];
                $discount = $entry->discount_value;
                if($discount > 0) {
                    $dataSummary['discount'] = $discount;
                }
                foreach ($payment as $entry) {
                    if ($shipping->payment_id == $entry->payment_id) {
                        $dataOrder['charge'] = $entry->charge;
                        $dataOrder['total'] = $dataOrder['total'] + $deliveryFee + $entry->charge;
                        $dataOrder['payment_total'] = $dataOrder['payment_total'] + $deliveryFee + $entry->charge;
                        $dataSummary['charge'] = $entry->charge;
                        break;
                    }
                }
                SDB::table('dtb_order')
                    ->where('order_id', $orderId)
                    ->update($dataOrder);
            }
            $addressCustomer = self::getAddrCustomer($orderId);
            $data = [
                'order_id' => $orderId,
                'products' => $products['data'],
                'user_info' => $address,
                'delivery' => $delivery,
                'payment' => $payment,
                'delivery_time' => $deliveryTime,
                'delivery_date' => $deliveryDates,
                'shipping' => $addressCustomer,
                'data_summary' => $dataSummary
            ];

            if (!empty($products['message'])) {
                $data['message'] = $products['message'];
            }
            return $data;
        }
        return null;
    }

    public static function insertOrder($request, $products = null, $payment = null, $delivery = null, $preOrderId = null)
    {
        $dataSummary = [
            'charge'         => '0',
            'shipping_price' => '0',
        ];
        $customerId = null;
        $totalAmount = $totalTax = $stocks = 0;
        foreach ($products as $index_p => $entry) {
            $totalAmount             += $entry->price * $entry->stock;
            $totalTax                += $entry->tax;
            $entry->price            = CommonHelper::getProductPrice($entry->price);
            $stocks                  += $entry->stock;
        }
        if($entry->discount_value > 0) {
            $dataSummary['discount'] = $entry->discount_value;
        }
        $baseInfor = SDB::table('dtb_base_info')->first();
        if ($baseInfor->delivery_free_quantity <= $stocks || $baseInfor->delivery_free_amount <= $totalAmount) {
            $deliveryFeeTotal = 0;
        } else {
            $deliveryFeeTotal = $delivery->fee;
        }
        $dataSummary['shipping_price'] = $deliveryFeeTotal;
        $dataOrderGer = [
            'device_type_id'     => 10,
            'payment_id'         => $payment->payment_id,
            'pre_order_id'       => $preOrderId,
            'subtotal'           => $totalAmount,
            'tax'                => $totalTax,
            'charge'             => $payment->charge,
            'total'              => $totalAmount - $entry->discount_value + $deliveryFeeTotal + $payment->charge,
            'payment_total'      => $totalAmount - $entry->discount_value + $deliveryFeeTotal + $payment->charge,
            'payment_method'     => $payment->payment_method,
            'delivery_fee_total' => $deliveryFeeTotal,
            'create_date'        => CommonHelper::dateNow(),
            'update_date'        => CommonHelper::dateNow(),
            'del_flg'            => ProductConst::NOT_DELETE,
            'status'             => ProductConst::STATUS_ORDER_DEFAULT
        ];
        $dataSummary['charge'] = $payment->charge;
        $dataSummary['total_price'] = $totalAmount - $entry->discount_value + $deliveryFeeTotal + $payment->charge;
        $dataSummary['sub_total'] = $totalAmount;

        if (Auth::guard('api')->check()) {
            $userInfor  = Auth::guard('api')->user();
            $customerId = $userInfor->customer_id;
            $dataOrder = [
                'customer_id'        => $customerId,
                'order_pref'         => $userInfor->pref,
                'order_sex'          => $userInfor->sex,
                'order_job'          => $userInfor->job,
                'order_name01'       => $userInfor->name01,
                'order_name02'       => $userInfor->name02,
                'order_kana01'       => $userInfor->kana01,
                'order_kana02'       => $userInfor->kana02,
                'order_company_name' => $userInfor->company_name,
                'order_email'        => $userInfor->email,
                'order_tel01'        => $userInfor->tel01,
                'order_tel02'        => $userInfor->tel02,
                'order_tel03'        => $userInfor->tel03,
                'order_fax01'        => $userInfor->fax01,
                'order_fax02'        => $userInfor->fax02,
                'order_fax03'        => $userInfor->fax03,
                'order_zip01'        => $userInfor->zip01,
                'order_zip02'        => $userInfor->zip02,
                'order_zipcode'      => $userInfor->zip01 . $userInfor->zip02,
                'order_addr01'       => $userInfor->addr01,
                'order_addr02'       => $userInfor->addr02,
                'order_birth'        => $userInfor->birth,
                'discount'           => $entry->discount_value,
            ];
        } else {
            $dataOrder = [
                'order_pref'         => $request->input('pref'),
                'order_sex'          => $request->input('sex'),
                'order_job'          => $request->input('job'),
                'order_name01'       => $request->input('name01'),
                'order_name02'       => $request->input('name02'),
                'order_kana01'       => $request->input('kana01'),
                'order_kana02'       => $request->input('kana02'),
                'order_company_name' => $request->input('company_name'),
                'order_email'        => $request->input('email'),
                'order_tel01'        => $request->input('tel01'),
                'order_tel02'        => $request->input('tel02'),
                'order_tel03'        => $request->input('tel03'),
                'order_fax01'        => $request->input('fax01'),
                'order_fax02'        => $request->input('fax02'),
                'order_fax03'        => $request->input('fax03'),
                'order_zip01'        => $request->input('zip01'),
                'order_zip02'        => $request->input('zip02'),
                'order_zipcode'      => $request->input('zip01') . $request->input('zip02'),
                'order_addr01'       => $request->input('addr01'),
                'order_addr02'       => $request->input('addr02'),
                'order_birth'        => $request->input('birth'),
            ];
        }
        $dataOrder = array_merge($dataOrder, $dataOrderGer);

        $orderId = SDB::table('dtb_order')->insertGetId($dataOrder);

        $data = [
            'products'     => $products,
            'order_id'     => $orderId,
            'customer_id'  => $customerId,
            'data_summary' => $dataSummary
        ];
        return $data;
    }

    public static function insertShipping($request, $customerId, $orderId, $delivery)
    {
        if (Auth::guard('api')->check()) {
            $shipping = SDB::table('dtb_customer_address')
                            ->where('customer_id', $customerId)
                            ->where('del_flg', ProductConst::NOT_DELETE)
                            ->orderBy('customer_address_id', 'ASC')
                            ->first();
            $dataShipping = [
                'shipping_pref'          => $shipping->pref,
                'order_id'               => $orderId,
                'delivery_id'            => $delivery->delivery_id,
                'fee_id'                 => $delivery->fee_id,
                'shipping_name01'        => $shipping->name01,
                'shipping_name02'        => $shipping->name02,
                'shipping_kana01'        => $shipping->kana01,
                'shipping_kana02'        => $shipping->kana02,
                'shipping_company_name'  => $shipping->company_name,
                'shipping_tel01'         => $shipping->tel01,
                'shipping_tel02'         => $shipping->tel02,
                'shipping_tel03'         => $shipping->tel03,
                'shipping_fax01'         => $shipping->fax01,
                'shipping_fax02'         => $shipping->fax02,
                'shipping_fax03'         => $shipping->fax03,
                'shipping_zip01'         => $shipping->zip01,
                'shipping_zip02'         => $shipping->zip02,
                'shipping_zipcode'       => $shipping->zipcode,
                'shipping_addr01'        => $shipping->addr01,
                'shipping_addr02'        => $shipping->addr02,
                'shipping_delivery_name' => $delivery->name,
                'shipping_delivery_fee'  => $delivery->fee,
                'create_date'            => CommonHelper::dateNow(),
                'update_date'            => CommonHelper::dateNow(),
                'del_flg'                => ProductConst::NOT_DELETE,
            ];
        } else {
            $dataShipping = [
                'shipping_pref'          => $request->input('pref'),
                'order_id'               => $orderId,
                'delivery_id'            => $delivery->delivery_id,
                'fee_id'                 => $delivery->fee_id,
                'shipping_name01'        => $request->input('name01'),
                'shipping_name02'        => $request->input('name02'),
                'shipping_company_name'  => $request->input('company_name'),
                'shipping_kana01'        => $request->input('kana01'),
                'shipping_kana02'        => $request->input('kana02'),
                'shipping_tel01'         => $request->input('tel01'),
                'shipping_tel02'         => $request->input('tel02'),
                'shipping_tel03'         => $request->input('tel03'),
                'shipping_fax01'         => $request->input('fax01'),
                'shipping_fax02'         => $request->input('fax02'),
                'shipping_fax03'         => $request->input('fax03'),
                'shipping_zip01'         => $request->input('zip01'),
                'shipping_zip02'         => $request->input('zip02'),
                'shipping_zipcode'       => $request->input('zip01') . $request->input('zip02'),
                'shipping_addr01'        => $request->input('addr01'),
                'shipping_addr02'        => $request->input('addr02'),
                'shipping_delivery_name' => $delivery->name,
                'shipping_delivery_fee'  => $delivery->fee,
                'create_date'            => CommonHelper::dateNow(),
                'update_date'            => CommonHelper::dateNow(),
                'del_flg'                => ProductConst::NOT_DELETE,
            ];
        }
        return SDB::table('dtb_shipping')->insertGetId($dataShipping);
    }

    public static function insertShipmentItem(&$products, $orderId, $shippingId, $productClassId, $deliveryDate)
    {
        $dataOrderDetail = $dataSelectionCalender = $dataShippingItem = [];
        foreach ($products as $index_p => $entry) {
            if ($entry->stock > 0) {
                $tempOrderDetail = [
                    'order_id' => $orderId,
                    'product_id' => $entry->product_id,
                    'product_class_id' => $entry->product_class_id,
                    'product_name' => $entry->name,
                    'product_code' => $entry->product_code,
                    'class_category_name1' => $entry->class_category_id1,
                    'class_category_name2' => $entry->class_category_id2,
                    'price' => $entry->price02,
                    'quantity' => $entry->stock,
                    'tax_rate' => $entry->tax_rate,
                    'tax_rule' => $entry->tax_rule,
                ];
                $tempShippingItem = [
                    'order_id' => $orderId,
                    'product_id' => $entry->product_id,
                    'product_class_id' => $entry->product_class_id,
                    'shipping_id' => $shippingId,
                    'product_name' => $entry->name,
                    'product_code' => $entry->product_code,
                    'class_category_name1' => $entry->class_category_id1,
                    'class_category_name2' => $entry->class_category_id2,
                    'price' => $entry->price02,
                    'quantity' => $entry->stock,
                ];
                $dataShippingItem[] = $tempShippingItem;
                $dataOrderDetail[] = $tempOrderDetail;
                $remove = ['price02', 'tax', 'tax_rule', 'tax_rate', 'discount_rate'];
                $products[$index_p] = CommonHelper::unset_multikey($entry, $remove);
            }
            $remove = ['price02', 'discount_rate', 'tax_rate', 'tax_rule', 'tax'];
            $products[$index_p] = CommonHelper::unset_multikey($entry, $remove);
        }
        if (count($dataOrderDetail) > 0) {
            SDB::table('dtb_order_detail')->insert($dataOrderDetail);
            $lastOrderId = SDB::getPdo()->lastInsertId();
            SDB::table('dtb_shipment_item')->insert($dataShippingItem);
        } else {
            return false;
        }
        $count = -1;
        for ($orderDetailId = $lastOrderId; $orderDetailId < $lastOrderId + count($dataOrderDetail); $orderDetailId++) {
            $count++;
            $tempCalender = [
                'order_detail_id' => $orderDetailId,
                'product_class_id' => $productClassId[$count],
                'calender_name' => CalenderConst::CALENDER_NAME,
                'selected_date' => $deliveryDate[$count],
                'create_date' => CommonHelper::dateNow(),
                'update_date' => CommonHelper::dateNow(),
            ];
            $dataSelectionCalender[] = $tempCalender;
        }
        if (count($dataSelectionCalender) > 0) {
            SDB::table('plg_selection_calender_order_detail')->insert($dataSelectionCalender);
        }
    }

    public static function customerUpdate($order){
        $customer = Auth::guard('api')->user();
        $dataUpdate = [
            'buy_times' => $customer->buy_times + 1,
            'last_buy_date'=> CommonHelper::dateNow(),
            'buy_total' => $customer->buy_total + (int)$order->payment_total,
        ];
        if(empty($customer->first_buy_date)){
            $dataUpdate['first_buy_date'] = CommonHelper::dateNow();
        }
        SDB::table('dtb_customer as c')
            ->where('c.customer_id',$customer->id)
            ->update($dataUpdate);
    }

    public static function orderUpdate($orderId){
        $dataUpdate = [
            'status' => 1,
            'order_date' => CommonHelper::dateNow()
        ];
        SDB::table('dtb_order')
            ->where('order_id',$orderId)
            ->whereIn('status',[7,8])
            ->update($dataUpdate);
    }

    public static function stockUpdate($products){
        foreach ($products as $product){
            SDB::table('dtb_product_class')
                ->where('product_class_id',$product->product_class_id)
                ->where('stock_unlimited','!=',ProductConst::STOCK_UNLIMITED)
                ->update([
                    'stock' => SDB::raw('stock - '.$product->stock)
                ]);
        }
    }
}
