<?php

namespace App\Core\Http\Middleware;

use App\Core\Helpers\AfterLoginHelper;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
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
        if (Auth::guard($guard)->check()) {
            $role =  Auth::user()->role_value;
            $this->redirectTo =  AfterLoginHelper::redirectInitPage($role);
            return redirect($this->redirectTo);
        }
        return $next($request);
    }
}
