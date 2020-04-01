<?php

namespace App\Api\V1\Services\Interfaces;

use Illuminate\Http\Request;

interface UserServiceInterface
{
    public function registerNormal($inputs);
    public function userInfo($userId);
    public function removeDeviceToken($userId,$deviceId);
    public function forgotPassword(array $inputs);
    public function userUpdate($inputs);
    public function deleteProductFavorite($id);
    public function deleteUser();
    public function orderHistory($inputs);
    public function orderDetail($id);
    public function getListFavorite($inputs);
    public function getPref();
    public function getJob();
    public function GetAddressByPostcode($inputs);
    public function getPrefAndJob();
    public function mailHistory($orderId);
    public function mailDetail($id);
}
