<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateStarSathiDealerMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $guards = empty($guards) ? [null] : $guards;
        if(!$request->has('sapcode'))
        {
            $request->session()->put('error', 'SAP Code required');
            return redirect(route('dealer.authenticate.error'));
        }
        if(!$request->has('authkey'))
        {
            $request->session()->put('error', 'Auth Key Required');
            return redirect(route('dealer.authenticate.error'));
        }
        return $next($request);
    }
}
