<?php

namespace App\Api\V1\Http\Controllers;
use App\Api\V1\Http\Requests\AddToCartRequest;
use App\Api\V1\Http\Requests\ContactRequest;
use App\Api\V1\Http\Requests\GetClassCategory2Request;
use App\Api\V1\Http\Requests\GetPriceClassProductRequest;
use App\Api\V1\Services\Interfaces\ProductServiceInterface;
use App\Core\Common\SDBStatusCode;
use App\Core\Entities\DataResultCollection;
use App\Core\Helpers\ResponseHelper;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $service;

    public function __construct (ProductServiceInterface $productService)
    {
        $this->service = $productService;
    }

    public function getList(Request $request){
        return ResponseHelper::JsonDataResult($this->service->getAll($request));
    }
    
    public function getListFromHtml(Request $request)
    {
        return ResponseHelper::JsonDataResult($this->service->getProductFromHtml($request));
    }

    public function getCart(Request $request){
        return ResponseHelper::JsonDataResult($this->service->getCart($request));
    }

    public function detail($id)
    {
        return ResponseHelper::JsonDataResult($this->service->detail($id));
    }

    public function addToFavorite($id)
    {
        return ResponseHelper::JsonDataResult($this->service->addToFavorite($id));
    }

    public function addToCart(AddToCartRequest $request)
    {
        return ResponseHelper::JsonDataResult($this->service->addToCart($request));
    }

    public function payment(Request $request)
    {
        return ResponseHelper::JsonDataResult($this->service->addToCart($request));
    }

    public function contact(ContactRequest $request)
    {
        if($request->input('action') != null) {
            $result = $this->service->contact($request);
        }else{
            $result         = new DataResultCollection();
            $result->status = SDBStatusCode::OK;
        }
        return ResponseHelper::JsonDataResult($result);
    }

    public function getContentContact(Request $request)
    {
        return ResponseHelper::JsonDataResult($this->service->getContentContact($request));
    }

    public function getClassCategory2(GetClassCategory2Request $request){
        return ResponseHelper::JsonDataResult($this->service->getclassCategory2($request));
    }

    public function getPriceClassProduct(GetPriceClassProductRequest $request){
        return ResponseHelper::JsonDataResult($this->service->getPriceClassProduct($request));
    }

    public function news()
    {
        return ResponseHelper::JsonDataResult($this->service->news());
    }
}
