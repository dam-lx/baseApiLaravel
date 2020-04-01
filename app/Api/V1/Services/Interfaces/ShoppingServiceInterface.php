<?php

namespace App\Api\V1\Services\Interfaces;

use Illuminate\Http\Request;

interface ShoppingServiceInterface
{
    public function detail($request);
    public function getPaymentInfo();
    public function payment($request);
    public function changeAddrShopping($inputs);
    public function changeDeliveryShopping($inputs);
    public function changePaymentShopping($inputs);
    public function changeInfoNonMember($inputs);
    public function order($request);
}
