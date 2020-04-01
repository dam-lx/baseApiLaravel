<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
<div style="white-space: pre;">
{{ $order[0]->name01 }} {{ $order[0]->name02 }} 様

{{ $template_mail->header }}

************************************************
ご請求金額
************************************************

ご注文番号：{{ $order[0]->id }}
お支払い合計：{{ $order[0]->payment_total}}
お支払い方法：{{ $order[0]->payment_method }}
メッセージ：{{ $order[0]->message }}


************************************************
　ご注文商品明細
************************************************

@foreach($order as $orderDetail)
商品コード: {{ $orderDetail->product_code }}
商品名: {{ $orderDetail->product_name }}  {{ $orderDetail->class_category_name1 }}  {{ $orderDetail->class_category_name2 }}
    {{--    単価： {{ calc_inc_tax($orderDetail->price, $orderDetail->tax_rate, $orderDetail->tax_rule) }}--}}
数量： {{ $orderDetail->quantity}}

@endforeach

-------------------------------------------------
小　計 {{ $order[0]->subtotal }}@if ($order[0]->tax > 0) (うち消費税 {{ $order[0]->tax }})
@endif

手数料 {{ $order[0]->charge }}
送　料 {{ $order[0]->delivery_fee_total}}
@if ($order[0]->discount > 0)
値引き {{ (0 - $order[0]->discount)}}
@endif
============================================
合　計 {{ $order[0]->payment_total }}

************************************************
　ご注文者情報
************************************************
お名前　：{{ $order[0]->name01 }} {{ $order[0]->name02 }} 様
フリガナ：{{ $order[0]->kana01 }} {{ $order[0]->kana02 }} 様
@if (!empty($order[0]->company_name))
会社名　：{{ $order[0]->company_name }}
@endif
{{--    {% if app.config.form_country_enable %}--}}
{{--    国　　　：{{ $order[0]->Country }}--}}
{{--    ZIPCODE ：{{ $order[0]->zipcode }}--}}
{{--    {% endif %}--}}
郵便番号：〒{{ $order[0]->zip01 }}-{{ $order[0]->zip02 }}
住所　　：{{ $order[0]->pref_name }}{{ $order[0]->addr01 }}{{ $order[0]->addr02 }}
電話番号：{{ $order[0]->tel01 }}-{{ $order[0]->tel02 }}-{{ $order[0]->tel03 }}
FAX番号 ：{{ $order[0]->fax01 }}-{{ $order[0]->fax02 }}-{{ $order[0]->fax03 }}

メールアドレス：{{ $order[0]->email }}

************************************************
　配送情報
************************************************

@foreach($shippings as $index => $shipping)
    {{--            ◎お届け先@if($order[0]->multiple) {{ loop.index }}@endif--}}

@if($index == 0)
お名前　：{{ $shipping->name01 }} {{ $shipping->name02 }} 様
フリガナ：{{ $shipping->kana01 }} {{ $shipping->kana02 }} 様
@if(!empty($shipping->company_name))
会社名　：{{ $shipping->company_name }}
@endif
{{--    国　　　：{{ $shipping->Country->name }}--}}
ZIPCODE ：{{ $shipping->zipcode }}
郵便番号：〒{{ $shipping->zip01 }}-{{ $shipping->zip02 }}
住所　　：{{ $shipping->pref_name }}{{ $shipping->addr01 }}{{ $shipping->addr02 }}
電話番号：{{ $shipping->tel01 }}-{{ $shipping->tel02 }}-{{ $shipping->tel03 }}
FAX番号 ：{{ $shipping->fax01 }}-{{ $shipping->fax02 }}-{{ $shipping->fax03 }}

お届け日：{{ empty($shipping->shipping_delivery_date) ? '指定なし' : $shipping->shipping_delivery_date }}
お届け時間：{{ $shipping->shipping_delivery_time? '指定なし' : $shipping->shipping_delivery_time}}
@endif
商品コード: {{ $shipping->product_code }}
商品名: {{ $shipping->product_name }}  {{ $shipping->class_category_name1 }}  {{ $shipping->class_category_name2 }}
数量：{{ $shipping->quantity }}

@endforeach

{{ $template_mail->footer }}
</div>
</body>
</html>
