<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\CategoryController;



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
    return response()->json(['status' => false,'msg'=>'Unauthrization']);
})->name('login');


Route::post('register', [AuthController::class, 'register']);
//Route::post('login', [AuthController::class, 'login']);

/*=====[ API ]=========*/
Route::group(['namespace' => 'App\Http\Controllers\API'],function(){
    //Version: V1
    Route::group(['prefix' => 'v1','namespace' =>'V1'],function(){
        Route::get('/get-all-dealer-rssd', ['uses' => 'MasonController@getAllMasonRssd']);
        Route::post('/send-query', ['uses' => 'QueryController@addQuery']);
        Route::get('/get-rewards', ['uses' => 'RewardController@getRewards'])->middleware(['auth:api']);
        Route::post('/get-dealers-by-mason', ['uses' => 'MasonController@getDealersByMasonId']);
        Route::get('/get-my-masons', ['uses' => 'MasonController@getMyMason'])->middleware(['auth:api']);
        Route::post('/get-lifting-history', ['uses' => 'LiftingController@liftingHistory'])->middleware(['auth:api']);
        Route::post('/update-lifting', ['uses' => 'LiftingController@updateLifting'])->middleware(['auth:api']);
        Route::post('/verify-phone', ['uses' => 'MasonRegistrationController@verifyPhone']);
        Route::get('/my-profile', ['uses' => 'UserController@myProfile'])->middleware(['auth:api']);
        Route::post('/update-profile', ['uses' => 'UserController@updateProfile'])->middleware(['auth:api']);
        Route::post('/change-profile-pic', ['uses' => 'UserController@changeProfilePic'])->middleware(['auth:api']);
        Route::get('/get-branchies', ['uses' => 'BranchController@getAllBranch'])->name('branchies');
        Route::post('/get-branch-dealer-rssd', ['uses' => 'BranchController@getBranchUser'])->name('get-branch-dealer-rssd');
        Route::post('/add-lifting', ['uses' => 'LiftingController@addLifting'])->middleware(['auth:api']);
        Route::get('/get-all-products', ['uses' => 'ProductController@getAllProduct']);
        Route::get('/get-about-us', ['uses' => 'UserController@getAbout'])->middleware(['auth:api']);
        Route::get('/privacy-policy', ['uses' => 'UserController@getPrivacy'])->middleware(['auth:api']);
        Route::get('/terms-and-conditions', ['uses' => 'UserController@getTerms'])->middleware(['auth:api']);
        Route::get('/get-contact', ['uses' => 'UserController@getContact'])->middleware(['auth:api']);
        Route::get('/get-faq', ['uses' => 'UserController@getFAQ'])->middleware(['auth:api']);
        // for TE
        Route::group(['prefix' => 'te','middleware' => ['auth:api']], function(){
            Route::post('/mason-register', ['uses' => 'MasonRegistrationController@register'])->name('mason-registration');
            Route::get('/get-branchies', ['uses' => 'BranchController@getAllBranch'])->name('branchies');
            Route::post('/get-branch-user', ['uses' => 'BranchController@getBranchUser'])->name('get-branch-user');
        });
       
        
        // auth 
        Route::group(['prefix' => 'auth','namespace' => 'Auth'],function(){
            Route::post('/register', ['uses' => 'AuthController@register']);
            Route::post('/send-otp', ['uses' => 'AuthController@sendOTP']);
            Route::post('/send-otp-to-new-number', ['uses' => 'AuthController@sendOTPToNewNumber']);
            Route::post('/login', ['uses' => 'AuthController@login'])->middleware(['appjson']);
            Route::post('/social/login', ['uses' => 'AuthController@socialLogin']);
            Route::post('/forgot-password', ['uses' => 'AuthController@forgotPassword']);
            Route::get('/logout', ['uses' => 'AuthController@logout'])->middleware(['appjson','auth:api']);
			
        });

        //my 
        Route::group(['prefix' => 'my','namespace' => 'My','middleware' => ['auth:api']],function(){
            //profile
            Route::get('/profile', ['uses' => 'ProfileController@profile'])->middleware(['appjson']);
            Route::post('/profile/update', ['uses' => 'ProfileController@profileUpdate'])->middleware(['appjson']);
            Route::post('/profile/update/pic', ['uses' => 'ProfileController@updateProfilePic'])->middleware(['appjson']);
        	 Route::get('/refercode', ['uses' => 'ReferCodeController@index']);
        	// address
        	Route::group(['prefix' => 'address'],function(){
            	 Route::post('/add', ['uses' => 'AddressController@addAddress'])->middleware(['appjson']);
            Route::post('/change', ['uses' => 'AddressController@chageAddress'])->middleware(['appjson']);
            	 Route::get('/list', ['uses' => 'AddressController@index']);
            });
        
        	// order
        	Route::group(['prefix' => 'order'],function(){
            	 Route::get('/list', ['uses' => 'OrderController@orderList']);
            	 Route::get('/{order_id}', ['uses' => 'OrderController@orederDetails']);
             	 Route::post('/place', ['uses' => 'OrderController@orderPlace'])->middleware(['appjson']);
            
            });
        });

        //church
        Route::group(['prefix' => 'church','namespace' => 'Church','middleware' => []],function(){
            Route::get('/list', ['uses' => 'ChurchController@index']);
        });
    
     //category
        Route::group(['prefix' => 'category','namespace' => 'Category','middleware' => []],function(){
            Route::get('/list', ['uses' => 'CategoryController@index']);
         Route::get('/{id}', ['uses' => 'CategoryController@productList']);
        });
    
     //product
         Route::group(['prefix' => 'product','namespace' => 'Product','middleware' => []],function(){
            Route::get('/list', ['uses' => 'ProductController@index']);
            Route::get('/{id}', ['uses' => 'ProductController@productDetail']);
            Route::post('/add', ['uses' => 'ProductController@addProduct'])->middleware(['appjson']);
           // Route::post('/active', ['uses' => 'CardController@activeCard'])->middleware(['appjson']);
        });
    
    
     //cart
         Route::group(['prefix' => 'cart','namespace' => 'Cart','middleware' => ['auth:api']],function(){
                Route::post('/cart-save', ['uses' => 'CartController@cartSave'])->middleware(['appjson']);
           Route::get('/cart-items/{id}', ['uses' => 'CartController@cartItems']);
         Route::get('/{id}', ['uses' => 'CartController@cartDelete']);
           // Route::post('/active', ['uses' => 'CardController@activeCard'])->middleware(['appjson']);
        });

        //settings
         Route::group(['prefix' => 'setting','namespace' => 'Setting','middleware' => []],function(){
            Route::get('/list', ['uses' => 'SettingController@index']);
            Route::post('/update/{id}', ['uses' => 'SettingController@changeSetting'])->middleware(['appjson']);
            Route::post('/add', ['uses' => 'SettingController@addSetting'])->middleware(['appjson']);
        });

        //card
        Route::group(['prefix' => 'card','namespace' => 'Card','middleware' => ['auth:api']],function(){
            Route::get('/list', ['uses' => 'CardController@index']);
            Route::post('/add', ['uses' => 'CardController@addCard'])->middleware(['appjson']);
            Route::post('/active', ['uses' => 'CardController@activeCard'])->middleware(['appjson']);
        });
    
    	//news
         Route::group(['prefix' => 'news','namespace' => 'News','middleware' => []],function(){
            Route::get('/list', ['uses' => 'NewsController@index']);
            Route::get('/{id}', ['uses' => 'NewsController@newsDetail']);
         	Route::post('/add', ['uses' => 'NewsController@addNews']);
           
        });
       //news
         Route::group(['prefix' => 'event','namespace' => 'Event','middleware' => []],function(){
            Route::get('/list', ['uses' => 'EventController@index']);
        });
    });
});




Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill('');
    return redirect('/home');
})->middleware(['auth:api', 'signed'])->name('verification.verify');
Route::apiResource('settings', SettingController::class)->middleware('auth:api');