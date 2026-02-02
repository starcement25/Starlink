<?php

namespace App\Http\Middleware;

use Closure;

class ifLoggedin
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
		if(session()->has('userdata'))
        {
            $role =  session()->get('role');
            if($role == 1)
            {
                return redirect()->route('admin.dashboard');
            }elseif($role==2)
            {
                return redirect()->route('admin.dashboard');
            }else
            {
                return redirect()->route('superadmin.dashboard');
            }
            
        }
        return $next($request);
    }
}
