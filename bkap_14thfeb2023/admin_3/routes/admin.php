<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\LiftingController;

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CatalogueController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\StaticPageController;
use App\Http\Controllers\Admin\ContactPageController;
use App\Http\Controllers\Admin\MasonCategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['as' => 'admin.', 'middleware' => ['guest']], function() {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('authenticate');
});



Route::group(['middleware' => ['preventBackHistory', 'auth']], function() {
    Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');
    Route::get('/home', [HomeController::class, 'home'])->name('admin.dashboard');

    // Products
    Route::resource('/products', ProductController::class);

    // Branches
    Route::resource('/branch', BranchController::class);

    // Users
    Route::resource('/users', UserController::class);
    
    // Liftings
    Route::resource('/liftings', LiftingController::class);
    Route::get('/verify/lifting', [LiftingController::class, 'verifyLiftings'])->name('verify.liftings');
    Route::post('/verify/liftings', [LiftingController::class, 'updateRewardStatus'])->name('verify.submit');
    Route::post('/verify/bulk/liftings', [LiftingController::class, 'updateBulkRewardStatus'])->name('verify.bulk.submit');

    // Catalogue
    Route::resource('/catalogues', CatalogueController::class);

    // Pages
    Route::resource('/pages', StaticPageController::class);

     // Contacts
    Route::resource('/contacts', ContactPageController::class);
    
    // Links
    Route::resource('/links', SocialLinkController::class);
    
    // Mason Category
    Route::resource('/mason-categories', MasonCategoryController::class);

    // Reports
    Route::get('/reports/mason/points', [ReportController::class, 'masonReports'])->name('mason.points');


});