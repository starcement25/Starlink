<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\Auth\LoginController;
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

Route::get('/', function () {
 echo base_path();
   // return view('welcome');
});

Route::group(['as' => 'admin.'], function() {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('authenticate');
});



Route::group(['middleware' => ['auth']], function() {
    Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');
    
    Route::resource('/products', ProductController::class);
    Route::resource('/branch', BranchController::class);
    Route::resource('/users', UserController::class);


});