<?php

namespace App\Core\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class Language
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $language = Session::get('locale', config('app.locale'));
        // dd($language);
        // Lấy dữ liệu lưu trong Session, không có thì trả về default lấy trong config
        config(['app.locale' => $language]);
        Lang::setLocale($language);
        return $next($request);
    }
}
