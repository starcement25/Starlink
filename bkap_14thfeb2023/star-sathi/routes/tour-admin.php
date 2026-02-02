<?php

use App\Http\Controllers\Tour\Admin\HomeController;
use App\Http\Controllers\Tour\Admin\PageController;
use App\Http\Controllers\Tour\Admin\PageDataController;
use App\Http\Controllers\Tour\Admin\PageContentController;
use App\Http\Controllers\Tour\Admin\Auth\TourLoginController;



Route::group(['as' => 'tour.', 'middleware' => ['guest_tour']], function() {
    Route::get('/login', [TourLoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [TourLoginController::class, 'authenticate'])->name('authenticate');
});

Route::group(['as' => 'tour.','middleware' => ['preventBackHistory', 'auth']], function() {
    Route::post('/logout', [TourLoginController::class, 'logout'])->name('logout');
    Route::get('/home', [HomeController::class, 'home'])->name('dashboard');
    Route::get('/registrations', [HomeController::class, 'showRegistrations'])->name('registration');

    Route::resource('/pages', PageController::class);
    
    // Page Contents.
    Route::get('/page-contents/page/{id}', [PageContentController::class, 'getPageContants'])->name('page.list');
    Route::get('/page-contents/{id}/edit', [PageContentController::class, 'editPageContents'])->name('page.list.item.edit');
    Route::post('/page-contents/{id}/edit', [PageContentController::class, 'updatePageContents'])->name('page.list.item.update');
    Route::get('/page-contents/{id}/create', [PageContentController::class, 'createPageContents'])->name('page.list.item.create');
    Route::post('/page-contents/{id}/create', [PageContentController::class, 'storePageContents'])->name('page.list.item.store');
   
    // Page Data
    Route::get('/page-data/page/{id}', [PageDataController::class, 'getPageDatas'])->name('page.data');

});