<?php
    
    namespace App\Api\V1\Http\Controllers;
    use App\Api\V1\Http\Requests\AddOrderRequest;
    use App\Api\V1\Http\Requests\AddrNonMemberRequest;
    use App\Api\V1\Http\Requests\CreateShippingAddressRequest;
    use App\Api\V1\Http\Requests\InforNonMemberRequest;
    use App\Api\V1\Http\Requests\PaymentRequest;
    use App\Api\V1\Services\Interfaces\ShoppingServiceInterface;
    use App\Core\Helpers\ResponseHelper;
    use Illuminate\Http\Request;

    class ShoppingController extends Controller
    {
        protected $service;
        public function __construct(ShoppingServiceInterface $shoppingService)
        {
            $this->service = $shoppingService;
        }
        
        public function detail(AddOrderRequest $request){
            return ResponseHelper::JsonDataResult($this->service->detail($request));
        }
    
        public function getPaymentInfo()
        {
            return ResponseHelper::JsonDataResult($this->service->getPaymentInfo());
        }
    
        public function payment(PaymentRequest $request)
        {
            return ResponseHelper::JsonDataResult($this->service->payment($request));
        }
        
        public function create(CreateShippingAddressRequest $request){
            $result = $this->service->create($request);
            return ResponseHelper::JsonDataResult($result);
        }
        
        public function edit($id, CreateShippingAddressRequest $request){
            $result = $this->service->edit($id,$request);
            return ResponseHelper::JsonDataResult($result);
        }
        
        public function index(){
            $result = $this->service->index();
            return ResponseHelper::JsonDataResult($result);
        }
        
        public function delete($id){
            $result = $this->service->delete($id);
            return ResponseHelper::JsonDataResult($result);
        }

        public function changeAddrShopping(Request $request){
            $result = $this->service->changeAddrShopping($request);
            return ResponseHelper::JsonDataResult($result);
        }

        public function changeDeliveryShopping(Request $request){
            return ResponseHelper::JsonDataResult($this->service->changeDeliveryShopping($request));
        }

        public function changeInfoNonMember(InforNonMemberRequest $request){
            $result = $this->service->changeInfoNonMember($request);
            return ResponseHelper::JsonDataResult($result);
        }

        public function order(Request $request){
            $result = $this->service->order($request);
            return ResponseHelper::JsonDataResult($result);
        }
        public function changePaymentShopping(Request $request){
            return ResponseHelper::JsonDataResult($this->service->changePaymentShopping($request));
        }
    }
