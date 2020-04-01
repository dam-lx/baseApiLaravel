<?php
    
    namespace App\Auth\Http\Controllers;
    
    use App\Auth\Models\User;
    use App\Core\Helpers\AfterLoginHelper;
    use Illuminate\Foundation\Auth\AuthenticatesUsers;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Log;
    
    class LoginController extends Controller
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
         * Where to redirect users after login.
         *
         * @var string
         */
        protected $redirectTo = '';
        
        /**
         * Create a new controller instance.
         *
         * @return void
         */
        public function __construct()
        {
            $this->redirectTo = route('backend.login');
            $this->middleware('guest')->except('logout');
        }
        public function username()
        {
            return 'email';
        }
        /**
         * Get the needed authorization credentials from the request.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return array
         */
        protected function credentials(Request $request)
        {
            $credentials = array($this->username() => $request->input($this->username()), 'password' => $request->input('password'), 'is_active' => 1);
            return $credentials;
        }
        /**
         * Handle a login request to the application.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
         */
        public function login(Request $request)
        {
            $this->validateLogin($request);
            // If the class is using the ThrottlesLogins trait, we can automatically throttle
            // the login attempts for this application. We'll key this by the username and
            // the IP address of the client making these requests into this application.
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);
                return $this->sendLockoutResponse($request);
            }
            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }
            $userName = $request->get($this->username());
            $client = User::where($this->username(), $userName)->first();
            $this->incrementLoginAttempts($request);
            // Customization: If client status is inactive (0) return failed_status error.
            if (isset($client->is_active) && $client->is_active === 0) {
                return $this->sendFailedLoginResponse($request, 'auth.not_active');
            }
            if (isset($client->role_value) && $client->role_value === 0) {
                return $this->sendFailedLoginResponse($request, 'auth.not_accept');
            }
            return $this->sendFailedLoginResponse($request);
        }
        
        /**
         * @param Request $request
         * @return \Illuminate\Http\RedirectResponse
         * overwrite exist function
         */
        protected function sendLoginResponse(Request $request)
        {
            $request->session()->regenerate();
            
            $this->clearLoginAttempts($request);
            
            if(!$this->authenticated($request, $this->guard()->user())){
                $role =  Auth::user()->role_value;
                $this->redirectTo =  AfterLoginHelper::redirectInitPage($role);
                return redirect()->intended($this->redirectTo);
            }else{
                return null;
            }
        }
        /**
         * Get the failed login response instance.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  string  $field
         * @return \Illuminate\Http\RedirectResponse
         */
        protected function sendFailedLoginResponse(Request $request, $trans = 'auth.login_failed')
        {
            $errors = [$this->username() => trans($trans)];
            if ($request->expectsJson()) {
                return response()->json($errors, 422);
            }
            return redirect()->back()
                             ->withInput($request->only($this->username(), 'remember'))
                             ->withErrors($errors);
        }
        
        /**
         * Log the user out of the application.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function logout(Request $request)
        {
            $this->guard()->logout();
            
            $request->session()->flush();
            
            $request->session()->regenerate();
            
            return redirect(route('backend.login'));
        }
    }
