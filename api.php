<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\SettingController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/unauthorization', function(){
    return response()->json(['statas' => false,'message'=>'Unauthrization']);
})->name('login');


Route::post('register', [AuthController::class, 'register']);
//Route::post('login', [AuthController::class, 'login']);

/*=====[ API ]=========*/
Route::group(['namespace' => 'App\Http\Controllers\API'],function(){
    //Version: V1
    Route::group(['prefix' => 'v1','namespace' =>'V1'],function(){

        // auth 
        Route::group(['prefix' => 'auth','namespace' => 'Auth'],function(){
            Route::post('/register', ['uses' => 'AuthController@register']);
            Route::post('/login', ['uses' => 'AuthController@login'])->middleware(['appjson']);
            Route::post('/social/login', ['uses' => 'AuthController@socialLogin']);
            Route::post('/forgot-password', ['uses' => 'AuthController@forgotPassword']);
            Route::post('/logout', ['uses' => 'AuthController@logout'])->middleware(['appjson','auth:api']);
        });

        //profile
        Route::group(['prefix' => 'my','namespace' => 'My','middleware' => ['appjson','auth:api']],function(){
            Route::get('/profile', ['uses' => 'ProfileController@profile']);
            Route::post('/profile/update', ['uses' => 'ProfileController@profileUpdate']);
            Route::post('/profile/update/pic', ['uses' => 'ProfileController@updateProfilePic']);
        });

        //church
        Route::group(['prefix' => 'church','namespace' => 'Church','middleware' => ['auth:api']],function(){
            Route::get('/list', ['uses' => 'ChurchController@index']);
        });

        //settings
         Route::group(['prefix' => 'setting','namespace' => 'Setting','middleware' => ['auth:api']],function(){
            Route::get('/list', ['uses' => 'SettingController@index']);
            Route::post('/update/{id}', ['uses' => 'SettingController@changeSetting']);
        });

        //card
        Route::group(['prefix' => 'card','namespace' => 'Card','middleware' => ['auth:api']],function(){
            Route::get('/list', ['uses' => 'CardController@index']);
            Route::post('/add', ['uses' => 'CardController@addCard'])->middleware(['appjson']);
        });
    });
});



//
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
 
    return redirect('/home');
})->middleware(['auth:api', 'signed'])->name('verification.verify');
Route::apiResource('settings', SettingController::class)->middleware('auth:api');