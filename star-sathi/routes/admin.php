<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\MasonController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\DealerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\LiftingController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\CatalogueController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\RedeemtionController;
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
   

    // Employees
    Route::resource('/employees', EmployeeController::class);
    Route::get('employees/bulk-upload/show', [EmployeeController::class, 'showBulkUploadForm'])->name('employee.upload.show');
    Route::post('employees/bulk-upload/show', [EmployeeController::class, 'uploadCsvFile'])->name('employee.upload.save');
    Route::post('employees/bulk-upload/progress', [EmployeeController::class, 'getProgress'])->name('employee.upload.progress');
   
    // Masons
    Route::resource('/masons', MasonController::class);
    Route::get('/masons/point/list', [MasonController::class, 'showMasonsPoint'])->name('point.list');
    Route::get('/masons/point-manipulate/{user}', [MasonController::class, 'showManupulateForm'])->name('point.manupulate');
    Route::post('/masons/point-manipulate', [MasonController::class, 'saveManupulation'])->name('point.save');
  
    // Dealer
    Route::resource('/dealers', DealerController::class);
    Route::get('dealers/bulk-upload/show', [DealerController::class, 'showBulkUploadForm'])->name('dealers.upload.show');
    Route::post('dealers/bulk-upload/show', [DealerController::class, 'uploadCsvFile'])->name('dealers.upload.save');
    Route::post('dealers/bulk-upload/progress', [DealerController::class, 'getProgress'])->name('dealers.upload.progress');
    
    // Liftings
    Route::resource('/liftings', LiftingController::class);
    Route::get('/verify/lifting', [LiftingController::class, 'verifyLiftings'])->name('verify.liftings');
    Route::post('/verify/liftings', [LiftingController::class, 'updateRewardStatus'])->name('verify.submit');
    Route::post('/verify/bulk/liftings', [LiftingController::class, 'updateBulkRewardStatus'])->name('verify.bulk.submit');

    // Support Master
     Route::resource('/supports', SupportController::class);
    
     // Zones Master
     Route::resource('/zones', ZoneController::class);

    // Redeemtion Master
     Route::resource('/redeemtions', RedeemtionController::class);
    
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