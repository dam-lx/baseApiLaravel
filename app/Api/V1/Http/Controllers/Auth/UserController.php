<?php

    namespace App\Api\V1\Http\Controllers\Auth;

    use App\Api\V1\Http\Controllers\Controller;
    use App\Api\V1\Http\Requests\ForgotPasswordRequest;
    use App\Api\V1\Http\Requests\GetAddressRequest;
    use App\Api\V1\Http\Requests\RegisterNormalRequest;
    use App\Api\V1\Http\Requests\UserUpdateRequest;
    use App\Api\V1\Models\User;
    use App\Api\V1\Services\Interfaces\UserServiceInterface;
    use App\Core\Common\ApiConst;
    use App\Core\Common\SDBStatusCode;
    use App\Core\Common\UserConst;
    use App\Core\Entities\DataResultCollection;
    use App\Core\Helpers\AuthHelper;
    use App\Core\Helpers\ResponseHelper;
    use Illuminate\Foundation\Auth\AuthenticatesUsers;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    class UserController extends Controller
    {
        /*
        |--------------------------------------------------------------------------
        | Login Controller
        |--------------------------------------------------------------------------
        |
        | This controller handles authenticating users for the application and
        | redirecting them to your home screen. The controller uses a trait
        | to conveniently provide its functionality to your applications.
        |
        */

        use AuthenticatesUsers;

        /**
         * Create a new controller instance.
         *
         * @return void
         */
        protected $userService;


        public function __construct (UserServiceInterface $userService)
        {
            $this->userService = $userService;
        }

        public function username()
        {
            return 'email';
        }

        protected function validateLogin(Request $request)
        {
            $rules = [
                $this->username() => 'required|email|max:320',
                'password'        => 'required|max:320',
            ];
    
            $customMessages = [
                $this->username() . ".required" => trans("auth.empty_username"),
                "password.required"             => trans("auth.empty_pass"),
            ];
            $this->validate($request, $rules, $customMessages);
        }

        /**
         * Handle a login request to the application.
         *
         * @param  \Illuminate\Http\Request $request
         * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
         */
        public function login (Request $request)
        {
            $response = new DataResultCollection();
            $this->validateLogin($request);
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);
                return $this->sendLockoutResponse($request);
            }
            $email    = $request->get($this->username());
            $client   = User::where($this->username(), $email)->first();
            if(!empty($client)){
                $salt     = $client->salt;
                if (AuthHelper::customLogin($request,$salt)){
                    $user        = Auth::user();
                    $email       = isset($user) && isset($user->email) ? $user->email : '';
                    $token       = $user->createToken('Login by email : ' . $email);
                    $accessToken = $token->accessToken;
                    $response->status = SDBStatusCode::OK;
                    $response->data   = array(ApiConst::ApiAccessTokenParamName  => $accessToken,
                                              ApiConst::UserInforResponseKeyName => AuthHelper::getUserInforById($user->getAuthIdentifier())
                    );
                } else {
                    $this->incrementLoginAttempts($request);
                    // Customization: If client status is inactive (0) return failed_status error.
                    if (empty($client)) {
                        $response->status  = SDBStatusCode::ApiError;
                        $response->message = trans('auth.can_not_login');
                    } else if (!isset($client->status) || $client->status != UserConst::active) {
                        $response->status  = SDBStatusCode::ApiError;
                        $response->message = trans('auth.not_active');
                    } else {
                        $response->status  = SDBStatusCode::ApiError;
                        $response->message = trans('auth.can_not_login');
                    }
                }
            }else{
                $response->status  = SDBStatusCode::ApiError;
                $response->message = trans('auth.can_not_login');
            }
            return ResponseHelper::JsonDataResult($response);
        }

        public function registerNormal(RegisterNormalRequest $request)
        {
            if($request->input('action') != null) {
                $result = $this->userService->registerNormal($request);
            }else{
                $result         = new DataResultCollection();
                $result->status = SDBStatusCode::OK;
            }
            return ResponseHelper::JsonDataResult($result);
        }
        
        public function forgotPassword(ForgotPasswordRequest $request){
            $result = $this->userService->forgotPassword($request->all());
            return ResponseHelper::JsonDataResult($result);
        }
        
        public function UserInfo ()
        {
            $result = new DataResultCollection();
            if (Auth::check()) {
                $result->status = SDBStatusCode::OK;
                $result->data   = AuthHelper::getUserInforById(Auth::user()->customer_id);
            } else {
                $result->status  = SDBStatusCode::ACLNotPass;
                $result->message = trans('auth.acl_not_access');
            }
            return ResponseHelper::JsonDataResult($result);
        }

        /**
         * revoke current token
         * @return \Illuminate\Http\JsonResponse
         */
        public function logout (Request $request)
        {
            $result         = new DataResultCollection();
            $result->status = SDBStatusCode::ApiAuthNotPass;
            if (Auth::check()) {
                $result->status = SDBStatusCode::OK;
                $accessToken    = Auth::user()->token();
                $accessToken->revoke();
            }
            return ResponseHelper::JsonDataResult($result);
        }

        /**
         * Get the needed authorization credentials from the request.
         *
         * @param  \Illuminate\Http\Request $request
         * @return array
         */
        protected function credentials (Request $request)
        {
            $credentials = array('email'    => $request->input('email'),
                                 'password' => $request->input('password'),
                                 'del_flg'  => UserConst::DELETED,
                                 'status'   => UserConst::active);
            return $credentials;
        }
        
        public function GetAddressByPostcode(GetAddressRequest $request){
            $result = $this->userService->GetAddressByPostcode($request);
            return ResponseHelper::JsonDataResult($result);
        }

        public function UserUpdate(UserUpdateRequest $request){
            $result = $this->userService->userUpdate($request);
            return ResponseHelper::JsonDataResult($result);
        }

        public function destroy(){
            $result = $this->userService->deleteUser();
            return ResponseHelper::JsonDataResult($result);
        }

        public function getListFavorite(Request $request){
            return ResponseHelper::JsonDataResult($this->userService->getListFavorite($request));
        }

        public function deleteProductFavorite($id=null){
            return ResponseHelper::JsonDataResult($this->userService->deleteProductFavorite($id));
        }

        public function orderHistory(Request $request){
            $result = $this->userService->orderHistory($request);
            return ResponseHelper::JsonDataResult($result);
        }
        
        public function orderDetail($id){
            $result = $this->userService->orderDetail($id);
            return ResponseHelper::JsonDataResult($result);
        }
        
        public function getPref(){
            $result = $this->userService->getPref();
            return ResponseHelper::JsonDataResult($result);
        }

        public function getJob(){
            $result = $this->userService->getJob();
            return ResponseHelper::JsonDataResult($result);
        }
        
        public function getPrefAndJob(){
            return ResponseHelper::JsonDataResult($this->userService->getPrefAndJob());
        }
        
        public function mailHistory($orderId){
            return ResponseHelper::JsonDataResult($this->userService->mailHistory($orderId));
        }

        public function mailDetail($id)
        {
            $result = $this->userService->mailDetail($id);
            return ResponseHelper::JsonDataResult($result);
        }

    }
