<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\DashboardController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\CategoryController;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StarLinkNotification;
use Carbon\Carbon;



use Illuminate\Foundation\Auth\EmailVerificationRequest;
/*
|--------------------------------------------------------------------------
| LIVE Routes
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/cache-clear', function () {
    Artisan::call('optimize:clear');
    Artisan::call('view:cache');
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    return 'Cache Cleared';
});
Route::middleware(['auth:api', 'account_status'])->get('/user', function (Request $request) {
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
        
        // To Show User Point
        Route::get('/user-point/{userId}', ['uses' => 'TestAPIController@userPoint']);

        Route::get('/get-all-dealer-rssd', ['uses' => 'MasonController@getAllMasonRssd']);
        Route::post('/send-query', ['uses' => 'QueryController@addQuery'])->middleware(['auth:api', 'account_status']);
        Route::post('/get-rewards', ['uses' => 'RewardController@getRewards'])->middleware(['auth:api', 'account_status']);
        Route::post('/get-dealers-by-mason', ['uses' => 'MasonController@getDealersByMasonId']);
        Route::get('/get-my-masons', ['uses' => 'MasonController@getMyMason'])->middleware(['auth:api', 'account_status']);
        Route::post('/get-lifting-history', ['uses' => 'LiftingController@liftingHistory'])->middleware(['auth:api', 'account_status']);
        Route::post('/update-lifting', ['uses' => 'LiftingController@updateLifting'])->middleware(['auth:api', 'account_status']);
        Route::post('/verify-phone', ['uses' => 'MasonRegistrationController@verifyPhone']);
        Route::get('/my-profile', ['uses' => 'UserController@myProfile'])->middleware(['auth:api', 'account_status']);
        Route::get('/user-profile', ['uses' => 'UserController@userProfile'])->withoutMiddleware([\Illuminate\Routing\Middleware\ThrottleRequests::class])->middleware(['auth:api','account_status']);
        Route::post('/update-profile', ['uses' => 'UserController@updateProfile'])->middleware(['auth:api', 'account_status']);

        // Order Feedback
        Route::post('/submit-order-feedback/{order_id}', ['uses' => 'RewardController@saveOrderFeedback'])->middleware(['auth:api', 'account_status']);
        
        // Order Acknowledgement
        Route::get('/check-user-last-order-acknowledgement', ['uses' => 'RewardController@checkUserLastOrderAcknowledgement'])->middleware(['auth:api', 'account_status']);

        Route::post('/update-user-preferences', ['uses' => 'UserController@updateUserPreferences'])->middleware(['auth:api', 'account_status']);
        Route::post('/change-profile-pic', ['uses' => 'UserController@changeProfilePic'])->middleware(['auth:api', 'account_status']);
        Route::get('/get-branchies', ['uses' => 'BranchController@getTeBranch'])->name('branchies')->middleware(['auth:api', 'account_status']);
        Route::post('/get-branch-dealer-rssd', ['uses' => 'BranchController@getBranchUser'])->name('get-branch-dealer-rssd');
        Route::post('/add-lifting', ['uses' => 'LiftingController@addLifting'])->middleware(['auth:api', 'account_status']);
        Route::post('/check-duplicate-lifting', ['uses' => 'LiftingController@checkDuplicateLifting'])->middleware(['auth:api', 'account_status']);
        Route::get('/lifting-enquiry', ['uses' => 'LiftingEnquiryController@getLiftingEnquiries'])->middleware(['auth:api', 'account_status']);
        Route::post('/lifting-enquiry', ['uses' => 'LiftingEnquiryController@doEnquiry'])->middleware(['auth:api', 'account_status']);
        // get Only Dealer Lists by mason branch
        Route::get('/get-dealers-by-mason-branch', ['uses' => 'MasonController@getDealersByMasonBranch'])->middleware(['auth:api', 'account_status']);
        // sent dealer linkage request
        Route::post('/dealer-linking-request', ['uses' => 'MasonController@dealerLinkingRequest'])->middleware(['auth:api', 'account_status']);
        Route::post('/add-lifting/starsathi', ['uses' => 'LiftingController@addLiftingByStarSathi'])->middleware(['starsathiapikey']);
        Route::get('/pending/liftings/starsathi/{sap_code}', ['uses' => 'LiftingController@pendingLiftingsByStarSathi'])->middleware(['starsathiapikey']);
        Route::get('/rejected/liftings/starsathi/{sap_code}', ['uses' => 'LiftingController@rejectedLiftingsByStarSathi'])->middleware(['starsathiapikey']);
        Route::get('/approved/liftings/starsathi/{sap_code}', ['uses' => 'LiftingController@approvedLiftingsByStarSathi'])->middleware(['starsathiapikey']);
        Route::post('/reject/lifting/starsathi', ['uses' => 'LiftingController@rejectLiftingByStarSathi'])->middleware(['starsathiapikey']);
            // Route::post('/lifting-add', ['uses' => 'LiftingController@liftingAdd']);
            Route::get('/correct/liftings', ['uses' => 'LiftingController@correctLiftings']);
            Route::get('/update/correct/liftings', ['uses' => 'LiftingController@updateCorrectLiftings']);
            Route::get('/update/correct/liftings/points', ['uses' => 'LiftingController@updateCorrectLiftingPoints']);
        Route::get('/get-all-products', ['uses' => 'ProductController@getAllProduct']);
        Route::get('/get-about-us', ['uses' => 'UserController@getAbout'])->middleware(['auth:api', 'account_status']);
        Route::get('/privacy-policy', ['uses' => 'UserController@getPrivacy'])->middleware(['auth:api', 'account_status']);
        Route::get('/terms-and-conditions', ['uses' => 'UserController@getTerms'])->middleware(['auth:api', 'account_status']);
        Route::get('/get-contact', ['uses' => 'UserController@getContact'])->middleware(['auth:api', 'account_status']);
        Route::get('/get-faq', ['uses' => 'UserController@getFAQ'])->middleware(['auth:api', 'account_status']);
        Route::get('/get-gift-catalog', ['uses' => 'UserController@getGiftLink'])->middleware(['auth:api', 'account_status']);
        Route::get('/get-gift-catalogues', ['uses' => 'UserController@getGiftCatalogues'])->middleware(['auth:api', 'account_status']);
        Route::get('/get-gifts-by-catalogue/{catalogueID}', ['uses' => 'UserController@getGiftsByCategory'])->middleware(['auth:api', 'account_status']);
        Route::get('/get-social-link', ['uses' => 'UserController@getSocialLink'])->middleware(['auth:api', 'account_status']);    
        Route::post('/get-notification', ['uses' => 'UserController@getNotification'])->middleware(['auth:api', 'account_status']);
        Route::post('/get-redeemtion', ['uses' => 'UserController@getRedeemtion'])->middleware(['auth:api', 'account_status']);
        Route::post('/apply-redeemtion', ['uses' => 'UserController@applyRedeemtion'])->middleware(['auth:api', 'account_status']);    
        Route::post('/get-rewards-by-mason', ['uses' => 'RewardController@getRewardsByMason'])->middleware(['auth:api', 'account_status']);    
        Route::post('/get-redeemtion-by-mason', ['uses' => 'UserController@getRedeemtionByMason'])->middleware(['auth:api', 'account_status']);
        Route::post('/get-order-by-mason', ['uses' => 'UserController@getOrderByMason'])->middleware(['auth:api', 'account_status']);
        Route::post('/confirm-gift-delivery/{order_id}', ['uses' => 'UserController@confirmGiftDelivery'])->middleware(['auth:api', 'account_status']);
       Route::post('/save-support', ['uses' => 'RewardController@saveSupport'])->middleware(['auth:api', 'account_status']);
       Route::post('/get-support', ['uses' => 'RewardController@getSupport'])->middleware(['auth:api', 'account_status']);
        Route::get('/view-banner','UserController@viewBanner');
         Route::get('/get-all-dealers', ['uses' => 'MasonController@getAllDealers'])->middleware(['auth:api', 'account_status']);

        Route::get('/push-note', ['uses' => 'UserController@sendNote']);    

        // Last Order Contact Details
        Route::post('/last-order-contact-details', ['uses' => 'UserController@lastOrderContactDetais'])->middleware(['auth:api', 'account_status']);

        // for TE
        Route::group(['prefix' => 'te','middleware' => ['auth:api', 'isTE', 'account_status']], function(){
            Route::post('/mason-register', ['uses' => 'MasonRegistrationController@register'])->name('mason-registration');
            Route::get('/get-branchies', ['uses' => 'BranchController@getAllBranch'])->name('branchies');
            Route::get('/get-te-branches', ['uses' => 'BranchController@getTeBranch'])->name('get-te-branches');
            Route::post('/get-branch-user', ['uses' => 'BranchController@getBranchUser'])->name('get-branch-user');
            Route::get('/get-dealer-linking_requests/{status}', ['uses' => 'StarSaathiController@getDealerLinkingRequest'])->name('get-dealer-linking_requests');
            Route::post('/accept/dealer-linking_requests', ['uses' => 'StarSaathiController@acceptDealerLinkingRequest'])->name('accept-dealer-linking_requests');
            Route::post('/reject/dealer-linking_requests', ['uses' => 'StarSaathiController@rejectDealerLinkingRequest'])->name('reject-dealer-linking_requests');
            Route::get('/starsaathi/get-pending-liftings', ['uses' => 'StarSaathiController@pendingLiftingsByStarSathi'])->name('get-starsaathi-pending-liftings');
            Route::get('/starsaathi/get-mason-options', ['uses' => 'StarSaathiController@masonsOptions'])->name('get-starsaathi-mason-options');
            Route::get('/starsaathi/get-te-mason-options', ['uses' => 'StarSaathiController@getTeMason'])->name('get-starsaathi-te-mason-options');
            Route::get('/starsaathi/edit-lifting', ['uses' => 'StarSaathiController@editLiftingByStarSathi'])->name('starsaathi-edit-lifting');
            Route::post('/starsaathi/save-edit-lifting', ['uses' => 'StarSaathiController@saveEditLiftingsByStarSathi'])->name('starsaathi-save-edit-lifting');
            Route::get('/starsaathi/get-accept-liftings', ['uses' => 'StarSaathiController@approvedLiftingsByStarSathi'])->name('get-starsaathi-accept-liftings');
            Route::post('/starsaathi/accept-lifting', ['uses' => 'StarSaathiController@approveLiftingByStarSathi'])->name('starsaathi-accept-lifting');
            Route::get('/starsaathi/get-reject-liftings', ['uses' => 'StarSaathiController@rejectLiftingsByStarSathi'])->name('get-starsaathi-reject-liftings');
            Route::post('/starsaathi/reject-lifting', ['uses' => 'StarSaathiController@rejectLiftingByStarSathi'])->name('starsaathi-reject-lifting');
            Route::get('/dashboard', ['uses' => 'DashboardController@dashboardTE'])->name('te-dashboard');
            Route::get('/dashboard/mason', ['uses' => 'DashboardController@masonDashboard'])->name('mason-dashboard');
            Route::get('/dashboard/lifting', ['uses' => 'DashboardController@liftingDashboard'])->name('lifting-dashboard');
            Route::get('/dashboard/liftingbags', ['uses' => 'DashboardController@liftingBagDashboard'])->name('lifting-bag-dashboard');
            Route::get('/dashboard/mason/netpoint', ['uses' => 'DashboardController@masonNetPointDashboard'])->name('mason-netpoint-dashboard');
            Route::get('/dashboard/status/gift', ['uses' => 'DashboardController@giftDashboard'])->name('gift-status-dashboard');
            Route::get('/dashboard/status/query', ['uses' => 'DashboardController@queryDashboard'])->name('gift-status-dashboard');
            Route::post('/update-mason/{masonId}', ['uses' => 'MasonController@updateMasonByTE'])->name('te-mason-update');
            Route::get('/get-te-mason/{masonId}', ['uses' => 'MasonController@getMasonByTE'])->name('te-mason');
            Route::get('/get-te-masons', ['uses' => 'MasonController@getMasonsByTE'])->name('te-masons');
        });
       
        // Settings
        Route::get('/settings', ['uses' => 'SettingsController@getAllSettings']);
        Route::get('/get-app-versions', ['uses' => 'SettingsController@appVersions']);
        Route::get('/get-flash-banner', ['uses' => 'SettingsController@getFlashBanner'])->middleware('auth:api', 'account_status');

        //mason list
        Route::get('/get-mason-list', ['uses' => 'DashboardController@getMasonList']);
        // Notification
        Route::get('/get-notifications', ['uses' => 'NotificationController@getNotifications'])->middleware('auth:api', 'account_status');

    
        // auth 
        Route::group(['prefix' => 'auth','namespace' => 'Auth'],function(){
            Route::post('/register', ['uses' => 'AuthController@register']);
            Route::post('/send-otp', ['uses' => 'AuthController@sendOTP']);
            Route::post('/send-otp-to-new-number', ['uses' => 'AuthController@sendOTPToNewNumber']);
            Route::post('/login', ['uses' => 'AuthController@login'])->middleware(['appjson']);
            // Route::post('/login', ['uses' => 'AuthController@testLogin'])->middleware(['appjson']);
            Route::post('/test-login', ['uses' => 'AuthController@testLogin'])->middleware(['appjson']);
            Route::post('/social/login', ['uses' => 'AuthController@socialLogin']);
            Route::post('/forgot-password', ['uses' => 'AuthController@forgotPassword']);
            Route::get('/logout', ['uses' => 'AuthController@logout'])->middleware(['appjson','auth:api', 'account_status']);
            Route::post('/account-delete', ['uses' => 'AuthController@deleteAccount'])->middleware(['appjson','auth:api', 'account_status']);
			
        });

        //my 
        Route::group(['prefix' => 'my','namespace' => 'My','middleware' => ['auth:api', 'account_status']],function(){
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
         Route::group(['prefix' => 'cart','namespace' => 'Cart','middleware' => ['auth:api', 'account_status']],function(){
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
        Route::group(['prefix' => 'card','namespace' => 'Card','middleware' => ['auth:api', 'account_status']],function(){
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


        // These APIs End Points Are Created For Testing Purpose For Making IOS App Release.
        Route::group(['prefix' => 'test'], function(){
            Route::post('/register-mason', ['uses' => 'TestController@register']);
            Route::post('/register-te', ['uses' => 'TestController@teRegister']);
            Route::get('/dealers', ['uses' => 'TestController@getAllTestDealers']);
            Route::get('/te', ['uses' => 'TestController@getTestTe']);
            Route::get('/branch', ['uses' => 'TestController@getTestTeBranch']);
            Route::get('/states', ['uses' => 'TestController@getStates']);
            Route::get('/all-branch', ['uses' => 'TestController@getBranches']);
        });
    });
});

/*=====[ API for 3rd party ]=========*/
Route::group(['namespace' => 'App\Http\Controllers\API'],function(){
    //Version: V1
    Route::group(['prefix' => 'v1/dealer','namespace' =>'V1'],function(){
        Route::get('/lifting-enquiry', ['uses' => 'StarSaathiController@getLiftingEnquiries']);
    });
});

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill('');
    return redirect('/home');
})->middleware(['auth:api', 'signed', 'account_status'])->name('verification.verify');
Route::apiResource('settings', SettingController::class)->middleware('auth:api', 'account_status');

Route::get('send-message', function () {
    $msg = "Lifting Bags: 140 PPC successfully Approved/Rejected: Approved. - Star Link";
    // $tmp = Http::get('https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to=7980625616&from=STARCM&text='.$msg.'&dlr-mask=19&dlr-url');
    $receiverNumber = 7980625616;
    // $msg = "Your Star Link verification code is: 1005 STAR CEMENT";// Transactional sms always have a text or msg format. if msg does not match with that format then sms will not trigger or it will generate error.
    $tmp = Http::get('https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to='.$receiverNumber.'&from=STARCM&text='.$msg.'&dlr-mask=19&dlr-url');
    return $tmp;
});
Route::get('send-notifications', function () {
    $user = \Auth::user();
    $msg = "Mason is now tagged with you.";
    $notificationData = [
        "notification_type" => "Testing Notifications.",
        "data" => [
            "msg" => "New Notification At ".Carbon::now(),
        ]
    ];
    Notification::send($user, new StarLinkNotification($notificationData));
    return "Notification Sent.";
})->middleware('auth:api', 'account_status');

//Delete User for uploading APP
Route::get('/user-delete', function(){
    return response()->json([
        "status" => true,
        "msg" => "User has been deleted successfully.",
    ]);
});


//SFA Site Lead and Conversion Tracking
 