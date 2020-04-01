<?php

namespace App\Api\V1\Http\Controllers;
use App\Api\V1\Http\Requests\CreateShippingAddressRequest;
use App\Api\V1\Services\Interfaces\ShippingServiceInterface;
use App\Core\Common\SDBStatusCode;
use App\Core\Dao\SDB;
use App\Core\Entities\DataResultCollection;
use App\Core\Helpers\ResponseHelper;
class ShippingController extends Controller
{
    protected $shippingService;
    public function __construct(ShippingServiceInterface $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    public function detail($id){
        return ResponseHelper::JsonDataResult($this->shippingService->detail($id));
    }

    public function create(CreateShippingAddressRequest $request){
        return ResponseHelper::JsonDataResult($this->shippingService->create($request));
    }

    public function edit($id, CreateShippingAddressRequest $request){
        return ResponseHelper::JsonDataResult($this->shippingService->edit($id,$request));
    }

    public function index(){
        return ResponseHelper::JsonDataResult($this->shippingService->index());
    }

    public function delete($id){
        return ResponseHelper::JsonDataResult($this->shippingService->delete($id));
    }
}
