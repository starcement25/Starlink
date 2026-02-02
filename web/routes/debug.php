<?php

use App\Http\Controllers\Debug\LiftingDebugController;




Route::group(['as' => 'debug.'], function() {
    Route::get('/test', [LiftingDebugController::class, 'testDebug']);
});