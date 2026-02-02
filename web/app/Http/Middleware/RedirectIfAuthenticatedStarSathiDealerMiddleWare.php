<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedStarSathiDealerMiddleWare
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
        if(!\Auth::check())
        {
            $request->session()->put('error', 'You are not logged in, Please Enter Authenticate URL.');
            return redirect(route('dealer.authenticate.error'));
        }
        $user = \Auth::user();
        if($user)
        {
            if(!in_array($user->role, [3,4,6]))
            {
                $request->session()->put([
                    'error' => 'Currently You are not logged in as a Dealer!! Please Enter Authenticate URL.'
                ]);
                return redirect(route('dealer.authenticate.error'));
            }
        }
        return $next($request);
    }
}
