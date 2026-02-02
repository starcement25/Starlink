<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\LiftingController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\StaticPageController;
use App\Http\Controllers\Admin\ContactPageController;
use App\Http\Controllers\Admin\CatalogueController;

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



Route::group(['middleware' => ['auth']], function() {
    Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');
    Route::get('/home', [HomeController::class, 'home'])->name('admin.dashboard');
    Route::resource('/products', ProductController::class);
    Route::resource('/branch', BranchController::class);
    Route::resource('/users', UserController::class);
    Route::resource('/liftings', LiftingController::class);
    Route::resource('/catalogues', CatalogueController::class);
    Route::resource('/pages', StaticPageController::class);
    Route::resource('/contacts', ContactPageController::class);
    Route::resource('/links', SocialLinkController::class);


});