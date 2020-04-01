<?php

namespace App\Api\V1\Services\Interfaces;

use Illuminate\Http\Request;

interface ProductServiceInterface
{
    public function getAll($request);
    public function getProductFromHtml($request);
    public static function getCart($id);
    public function addToFavorite($id);
    public function addToCart($request);
    public function contact($request);
    public function detail($id);
    public function getclassCategory2($request);
    public function getPriceClassProduct($inputs);
    public function getContentContact($inputs);
    public function news();
}
