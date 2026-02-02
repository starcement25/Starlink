<?php

namespace App\Http\Middleware;

use Closure;

class CheckLoginForSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!session()->has('userdata'))
		{
			return redirect()->route('superadminlogin');
		}
        return $next($request);
    }
}
