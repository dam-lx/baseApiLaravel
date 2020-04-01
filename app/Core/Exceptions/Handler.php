<?php
    
    namespace App\Core\Exceptions;
    
    use App\Core\Common\SDBStatusCode;
    use App\Core\Entities\DataResultCollection;
    use Exception;
    use Illuminate\Auth\AuthenticationException;
    use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Validation\ValidationException;
    
    class Handler extends ExceptionHandler
    {
        /**
         * A list of the exception types that are not reported.
         *
         * @var array
         */
        protected $dontReport = [
            \League\OAuth2\Server\Exception\OAuthServerException::class
        ];
        
        /**
         * A list of the inputs that are never flashed for validation exceptions.
         *
         * @var array
         */
        protected $dontFlash = [
            'password',
            'password_confirmation',
        ];
        
        /**
         * Report or log an exception.
         *
         * @param  \Exception  $exception
         * @return void
         */
        public function report(Exception $exception)
        {
            parent::report($exception);
        }
        
        /**
         * Render an exception into an HTTP response.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Exception  $exception
         * @return \Illuminate\Http\Response
         */
        public function render($request, Exception $exception)
        {
            return parent::render($request, $exception);
        }
        /**
         * @param \Illuminate\Http\Request $request
         * @param ValidationException $exception
         * @return \Illuminate\Http\JsonResponse
         * Overwrite
         */
        protected function invalidJson($request, ValidationException $exception)
        {
            $result =  new DataResultCollection();
            $result->status = SDBStatusCode::ApiError;
            $result->message = trans('common.title_error');
            $result->data = $exception->errors();
            if(config('app.debug')==true){
                Log::error(print_r( $result->data,true));
            }
            return response()->json($result, 200);
        }
        /**
         * Convert a validation exception into a response.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Illuminate\Validation\ValidationException  $exception
         * @return \Illuminate\Http\Response
         */
        protected function invalid($request, ValidationException $exception)
        {
            if(config('app.debug')==true){
                Log::error(print_r( $exception->errors(),true));
            }
            Auth::logout();
            return redirect($exception->redirectTo ?? route('backend.login'))
                ->withInput($request->except($this->dontFlash))
                ->withErrors($exception->errors(), $exception->errorBag);
        }
        /**
         * @param \Illuminate\Http\Request $request
         * @param AuthenticationException $exception
         * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
         * not passed authen ( miss token, incorrect token....) return
         */
        protected function unauthenticated($request, AuthenticationException $exception)
        {
            if ($request->expectsJson()) {
                $result =  new DataResultCollection();
                $result->status = SDBStatusCode::ApiAuthNotPass;
                $result->message = trans('common.unauthenticated');
                return response()->json($result, 200);
            }
            return redirect()->route('backend.login');
        }
    }
