<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventDealerLogin
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
        $user = \Auth::user();
        if($user)
        {
            if(in_array($user->role, [3,4,6]))
            {
                $request->session()->put([
                    'logout' => 1,
                    'error' => 'Currently You are logged in as a Dealer!! Please Log out to Continue.'
                ]);
                return redirect(route('dealer.authenticate.error'));
            }
        }
        return $next($request);
    }
}
