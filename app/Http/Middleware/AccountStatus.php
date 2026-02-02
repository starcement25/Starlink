<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Utils\LocalLanguageTranslation;

class AccountStatus
{
    protected $localLanguageTranslate;

    public function __construct(LocalLanguageTranslation $localLanguageTranslate)
    {
        $this->localLanguageTranslate = $localLanguageTranslate;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $targetLanguage = null;
        if($request->has("preferred_app_lang") && !empty($request->preferred_app_lang))
        {
            $targetLanguage = $request->preferred_app_lang;
        }

        if(empty($targetLanguage) && request()->user() != null && !empty(request()->user()->preferred_app_lang))
        {
            $targetLanguage = request()->user()->preferred_app_lang;
        }
        // if(\Auth::check() && \Auth::user()->status == 1)
        // {
        //     return $next($request);
        // }
        if(request()->user() != null && request()->user()->status == 1)
        {
            return $next($request);
        }
        return response()->json([
            'success' => false,
            'status_code' => 401,
            'message' => $this->localLanguageTranslate->translate("Your_account_may_be_disabled,_please_try_with_another_credentials", $targetLanguage),
        ]);
    }
}
