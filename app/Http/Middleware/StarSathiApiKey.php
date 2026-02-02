<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use Illuminate\Http\Request;

class StarSathiApiKey
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
        $starSathiApiKey = Setting::where('setting_name', 'star_sathi_api_key')->pluck('setting_value')->first();
        $appKeyHeader = $request->header('app-key');
        if ($appKeyHeader === null) {
            $re_message = 'app-key required in header';
            return response()->json(['message' => $re_message], 403);
        }
        if($appKeyHeader != $starSathiApiKey)
        {
            $re_message = 'Invalid app-key.';
            return response()->json(['message' => $re_message], 403);
        }
        return $next($request);
    }
}
