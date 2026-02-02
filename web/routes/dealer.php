<?php

use App\Http\Controllers\StarSathi\DealerController;
use App\Http\Controllers\StarSathi\NotificationController;
use App\Http\Controllers\StarSathi\AutomateLiftingController;



Route::group(['as' => 'dealer.', 'middleware' => ['auth_star_sathi_dealer', 'preventBackHistory']], function() {
    Route::get('/authenticate', [DealerController::class, 'authenticate'])->name('authenticate');
    
});
Route::get('/error', [DealerController::class, 'error'])->name('dealer.authenticate.error');
Route::get('/lifting/automate', [AutomateLiftingController::class, 'automateLifting'])->name('lifting.automate');
Route::get('/get/specific/report/verify-lifting', [AutomateLiftingController::class, 'getSpecificMonthVerifyLiftingReport'])->name('specific.lifting.report.automate');
Route::get('/automate/report/verify-lifting', [AutomateLiftingController::class, 'automateVerifyLiftingReport'])->name('lifting.report.automate');

// Route::get('/automate/report/test-verify', [AutomateLiftingController::class, 'checkAutomateVerifyLiftingReport'])->name('test.lifting.report.automate');

Route::get('/automate/report/annual-verify-lifting', [AutomateLiftingController::class, 'automateVerifyLiftingAnnualReport'])->name('lifting.annual.report.automate');
Route::get('/automate/ledger/export', [AutomateLiftingController::class, 'automateExportLedger'])->name('ledger.export.automate');
Route::group(['as' => 'dealer.','middleware' => ['star_sathi_dealer', 'preventBackHistory']], function() {
    Route::post('/logout', [DealerController::class, 'dealerLogout'])->name('logout');
    Route::get('/dashboard', [DealerController::class, 'dashboard'])->name('dashboard');
    Route::get('/notification', [NotificationController::class, 'getNotifications'])->name('notification');
    Route::get('/pending/liftings', [DealerController::class, 'pendingLiftingsByStarSathi'])->name('pending.liftings');
    Route::get('/pending/liftings/{id}', [DealerController::class, 'editPendingLiftingsByStarSathi'])->name('pending.liftings.edit');
    Route::post('/pending/liftings/{id}', [DealerController::class, 'saveEditPendingLiftingsByStarSathi'])->name('save.pending.liftings.edit');
    Route::get('/accept/liftings', [DealerController::class, 'approvedLiftingsByStarSathi'])->name('accept.liftings');
    Route::get('/reject/liftings', [DealerController::class, 'rejectedLiftingsByStarSathi'])->name('reject.liftings');
    Route::get('/lifting/reject/{lifting_id}', [DealerController::class, 'viewRejectLiftingByStarSathi'])->name('view.lifting.reject');
    Route::post('/lifting/reject', [DealerController::class, 'rejectLiftingByStarSathi'])->name('lifting.reject');
    Route::get('/lifting/accept/{lifting_id}', [DealerController::class, 'viewAddLiftingByStarSathi'])->name('view.lifting.accept');
    Route::post('/lifting/accept', [DealerController::class, 'addLiftingByStarSathi'])->name('lifting.accept');
});