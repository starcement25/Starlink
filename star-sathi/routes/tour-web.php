<?php

use App\Http\Controllers\Tour\Web\PageController;
use App\Http\Controllers\Tour\Admin\PageContentController;

Route::get('pages/{slug}', [PageController::class,'renderPage']);
Route::post('pages', [PageController::class,'saveData'])->name('page.submit');
Route::get('/dealer/codes', [PageController::class, 'getDealers'])->name('get.dealers');
Route::get('/dealer/by-code/', [PageController::class, 'getDealerByCode'])->name('dealer.info');

