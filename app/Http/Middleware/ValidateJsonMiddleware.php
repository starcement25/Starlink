<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateJsonMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isJson()) {
            json_decode($request->getContent());

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'message' => 'Invalid or malformed JSON.',
                ], 400);
            }
        }

        return $next($request);
    }
}
