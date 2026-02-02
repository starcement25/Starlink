<?php

use App\Http\Controllers\Tour\Admin\HomeController;
use App\Http\Controllers\Tour\Admin\Auth\TourLoginController;



Route::group(['as' => 'tour.', 'middleware' => ['guest_tour']], function() {
    Route::get('/login', [TourLoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [TourLoginController::class, 'authenticate'])->name('authenticate');
});

Route::group(['as' => 'tour.','middleware' => ['preventBackHistory', 'auth']], function() {
    Route::post('/logout', [TourLoginController::class, 'logout'])->name('logout');
    Route::get('/home', [HomeController::class, 'home'])->name('dashboard');
    Route::get('/registrations', [HomeController::class, 'showRegistrations'])->name('registration');

});