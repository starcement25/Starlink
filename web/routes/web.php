<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ASMController;
use App\Http\Controllers\Admin\MasonController;

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
    return view('welcome');
});

Route::get('/asm/lifting/action/{lifting}/{taken}', [ASMController::class, 'liftingActionTaken'])->name('asm.lifting.action');

//Route::resource('tests', App\Http\Controllers\TestController::class);
//Route::resource('dealers', App\Http\Controllers\DealerController::class);
