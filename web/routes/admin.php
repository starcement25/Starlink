<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\MasonController;
use App\Http\Controllers\Admin\DealerLinkRequestController;
use App\Http\Controllers\Admin\ASMController;
use App\Http\Controllers\Admin\BannerController;
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
use App\Http\Controllers\Admin\CustomerLiftingController;

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

Route::group(['as' => 'admin.', 'middleware' => ['guest', 'prevent_dealer']], function() {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('authenticate');
});



Route::group(['middleware' => ['preventBackHistory', 'auth', 'prevent_dealer']], function() {
    Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');
    Route::get('/home', [HomeController::class, 'home'])->name('admin.dashboard');

    
    //Clean DataBase Records based on client requirement.
    Route::get('clean/db/records', [UserController::class, 'cleanDbRecords'])->name('clean.records');

    //Dashboard
    Route::any('/dashboard', [DashboardController::class, 'dashboard'])->name('admin.employee.dashboard');
    Route::any('/dashboard/mason', [DashboardController::class, 'masonDashboard'])->name('admin.mason.dashboard');
    Route::any('/dashboard/mason/points', [DashboardController::class, 'masonPointDashboard'])->name('admin.mason.point.dashboard');
    Route::any('/dashboard/liftings', [DashboardController::class, 'liftingDashboard'])->name('admin.lifting.dashboard');
    Route::any('/dashboard/redeemtion', [DashboardController::class, 'redeemtionDashboard'])->name('admin.redeemtion.dashboard');
    Route::any('/dashboard/support', [DashboardController::class, 'supportDashboard'])->name('admin.support.dashboard');
    // Products
    Route::resource('/products', ProductController::class);

    // Branches
    Route::resource('/branch', BranchController::class);
    Route::get('branch/bulk-upload/show', [BranchController::class, 'showBulkUploadForm'])->name('branch.upload.show');
    Route::post('branch/bulk-upload/show', [BranchController::class, 'uploadCsvFile'])->name('branch.upload.save');
    Route::post('branch/bulk-upload/progress', [BranchController::class, 'getProgress'])->name('branch.upload.progress');

    // Users
    Route::resource('/users', UserController::class);
    Route::get('/ajax/search-user', [UserController::class, 'searchUser'])->name('ajax.search-user');
    Route::get('/ajax/get-te-masons/{teID}', [UserController::class, 'getTeMasons'])->name('ajax.get-te-masons');
    //Manually Update User Net Points
    Route::get('/update/user/netpoint', [UserController::class, 'updateUserNetPoint']);
   

    // Employees
    Route::resource('/employees', EmployeeController::class);
    Route::get('employees/bulk-upload/show', [EmployeeController::class, 'showBulkUploadForm'])->name('employee.upload.show');
    Route::post('employees/bulk-upload/show', [EmployeeController::class, 'uploadCsvFile'])->name('employee.upload.save');
    Route::post('employees/bulk-upload/progress', [EmployeeController::class, 'getProgress'])->name('employee.upload.progress');

    // ASM
    Route::resource('/asm', ASMController::class);
    Route::get('/asm/export/data', [ASMController::class, 'export'])->name('asm.export');
    //ASM bulk import
        Route::get('asm/bulk-upload/show', [ASMController::class, 'showBulkUploadForm'])->name('asm.upload.show');
        Route::post('asm/bulk-upload/show', [ASMController::class, 'uploadCsvFile'])->name('asm.upload.save');
        Route::post('asm/bulk-upload/progress', [ASMController::class, 'getProgress'])->name('asm.upload.progress');

   
    // Masons
    Route::resource('/masons', MasonController::class);
    // Transfer Mason
    Route::get('/transfer/mason', [MasonController::class, 'masonTransferInterface'])->name('transfer.masons');
    Route::post('/transfer/mason', [MasonController::class, 'masonTransfer'])->name('transfer.masons');
    // End of Transfer Mason

    Route::get('/masons/point/list', [MasonController::class, 'showMasonsPoint'])->name('point.list');
    Route::get('/masons/export/data', [MasonController::class, 'export'])->name('mason.export');
     //Route::get('/update-mason-reg-point', [MasonController::class, 'upDateMasonRegP']);
    Route::get('/masons/point-manipulate/{user}', [MasonController::class, 'showManupulateForm'])->name('point.manupulate');
    Route::post('/masons/point-manipulate', [MasonController::class, 'saveManupulation'])->name('point.save');
    //mason bulk import
        Route::get('masons/bulk-upload/show', [MasonController::class, 'showBulkUploadForm'])->name('mason.upload.show');
        /////////////////
        //   Route::get('masons/bulk-upload-point/show', [MasonController::class, 'showBulkUploadsForm'])->name('mason.upload-point.show');
        //   Route::post('masons/point/bulk-upload', [MasonController::class, 'bulkPointManipulation'])->name('point.bulk.upload');
          //////////////
        Route::post('masons/bulk-upload/show', [MasonController::class, 'uploadCsvFile'])->name('mason.upload.save');
        Route::post('masons/bulk-upload/progress', [MasonController::class, 'getProgress'])->name('mason.upload.progress');
        //mason bulk aadhaar doc upload
    Route::get('masons/bulk-image-upload/show', [MasonController::class, 'showAadhaarDocUploadForm'])->name('mason.aadhaar.show');
    Route::post('masons/bulk-image-upload', [MasonController::class, 'uploadAadhaarDoc'])->name('mason.aadhaar.upload');

    
    // Dealer Link Requests
    Route::resource('/dealerlinkrequests', DealerLinkRequestController::class);
    Route::get('/dealerlinkrequest/export', [DealerLinkRequestController::class, 'export'])->name('dealerlinkrequest.export');

     /////////////////
          Route::get('masons/bulk-upload-point/show', [MasonController::class, 'showBulkUploadsForm'])->name('mason.upload-point.show');
          Route::post('masons/point/bulk-upload', [MasonController::class, 'bulkPointManipulation'])->name('point.bulk.upload');
            Route::post('masons/bulk-upload/progress', [MasonController::class, 'getProgresses'])->name('mason.upload.progress');
          //////////////

    // Mason Ledger
    Route::get('/get-mason-dropdown-options', [MasonController::class, 'getMasonDropDownOptions'])->name('mason.dropdown.options');
    Route::get('/ledger', [MasonController::class, 'showLedger'])->name('mason.ledger');
    Route::get('/fetch/ledger', [MasonController::class, 'getLedger'])->name('fetch.ledger');
    Route::get('/masons/ledger/export/{mason}', [MasonController::class, 'exportLedger'])->name('mason.ledger.export');
    Route::get('/masons/ledger/export', [MasonController::class, 'exportAllLedger'])->name('all.ledger.export');
  
    // Dealer
    Route::resource('/dealers', DealerController::class);
    Route::get('dealers/bulk-upload/show', [DealerController::class, 'showBulkUploadForm'])->name('dealers.upload.show');
    Route::get('dealers/sap/upload/show', [DealerController::class, 'showSapUploadForm'])->name('dealers.upload.sap.show');
    Route::post('dealers/bulk-upload/show', [DealerController::class, 'uploadCsvFile'])->name('dealers.upload.save');
    Route::post('dealers/sap-upload/show', [DealerController::class, 'uploadSapCsvFile'])->name('dealers.sap.upload.save');
    Route::post('dealers/bulk-upload/progress', [DealerController::class, 'getProgress'])->name('dealers.upload.progress');
    Route::post('dealers/sap-upload/progress', [DealerController::class, 'getSapProgress'])->name('dealers.sap.upload.progress');
    Route::get('dealers/fetch/{branch_id}', [DealerController::class, 'getDealersByBranch'])->name('dealers.fetch.branch_id');
    Route::get('/dealers/export/data', [DealerController::class, 'dealerExport'])->name('dealer.export');
    
    // Liftings
    Route::resource('/liftings', LiftingController::class);
    Route::get('/liftings/export/data', [LiftingController::class, 'liftingExport'])->name('lifting.export');
    Route::get('/verify/lifting', [LiftingController::class, 'verifyLiftings'])->name('verify.liftings');
    Route::get('/verify/lifting/bulk-update/show', [LiftingController::class, 'showBulkLiftingUpdateForm'])->name('verify.liftings.bulk.update');
    Route::post('/verify/lifting/bulk-update/show', [LiftingController::class, 'saveBulkLiftingUpdateForm'])->name('verify.liftings.bulk.update.save');
    Route::post('/verify/lifting/bulk-update/progress', [LiftingController::class, 'bulkLiftingUpdateProgress'])->name('verify.liftings.bulk.update.progress');
    Route::get('/verify/lifting/mason/search/{searchval?}', [LiftingController::class, 'searchMason'])->name('verify.liftings.mason.search');
    Route::get('/verify/lifting/edit/{lifting_id}', [LiftingController::class, 'editVerifyLiftings'])->name('verify.liftings.edit');
    Route::patch('/verify/lifting/edit/{lifting_id}', [LiftingController::class, 'updateRewardStatus'])->name('verify.liftings.edit');
    Route::post('/verify/liftings', [LiftingController::class, 'updateRewardStatus'])->name('verify.submit');
    Route::post('/verify/bulk/liftings', [LiftingController::class, 'updateBulkRewardStatus'])->name('verify.bulk.submit');
    Route::post('/verify/liftings/download', [LiftingController::class, 'downloadExcel'])->name('verify.liftings.download');
    Route::post('/verify/liftings/download/path', [LiftingController::class, 'downloadFileFromPath'])->name('verify.liftings.download.filepath');
    Route::get('/verify/liftings/download/percentage', [LiftingController::class, 'getExcelDownloadingProgressPercentage'])->name('verify.liftings.download.percentage');
    Route::get('/csv-progress', [LiftingController::class, 'csvProgress'])->name('csv.progress');
        //Correcting Wrong Liftings
        Route::get('/wrong-liftings', [LiftingController::class, 'wrongLiftings']);
        //update bulk lifting
        Route::get('/bulk-liftings-update', [LiftingController::class, 'updateBulkLiftings']);

    // Support Master
     Route::resource('/supports', SupportController::class);
    
     // Zones Master
     Route::resource('/zones', ZoneController::class);

    // Redeemtion Master
     Route::resource('/redeemtions', RedeemtionController::class);
     Route::get('/redeemtions/export/data', [RedeemtionController::class, 'export'])->name('redeemtion.export');
    //Redeemtion bulk import
    Route::get('redeemtions/bulk-upload/show', [RedeemtionController::class, 'showBulkUploadForm'])->name('redeemtion.upload.show');
    Route::post('redeemtions/bulk-upload/show', [RedeemtionController::class, 'uploadCsvFile'])->name('redeemtion.upload.save');
    Route::post('redeemtions/bulk-upload/progress', [RedeemtionController::class, 'getProgress'])->name('redeemtion.upload.progress');
    
     // Catalogue
    Route::resource('/catalogues', CatalogueController::class);
    Route::get('catalogues/bulk-upload/show', [CatalogueController::class, 'showBulkUploadForm'])->name('catalogue.upload.show');
    Route::post('catalogues/bulk-upload/show', [CatalogueController::class, 'uploadCsvFile'])->name('catalogue.upload.save');
    Route::post('catalogues/bulk-upload/progress', [CatalogueController::class, 'getProgress'])->name('catalogue.upload.progress');
    Route::get('/catalogues/export/data', [CatalogueController::class, 'cataloguesExport'])->name('catalogue.export');

    Route::get('/catalogues/download/format', [CatalogueController::class, 'downloadCatalogueFormat'])->name('catalogue.format.download');
    Route::get('/catalogues/bulk-update/status', [CatalogueController::class, 'bulkUpdateStatusShow'])->name('catalogue.bulk-update.status.show');
    Route::post('/catalogues/bulk-update/status', [CatalogueController::class, 'updateBulkStatus'])->name('catalogue.bulk-update.status.update');

     // Banners
    Route::resource('/banners', BannerController::class);

    // Pages
    Route::resource('/pages', StaticPageController::class);

     // Contacts
    Route::resource('/contacts', ContactPageController::class);
    
    // Links
    Route::resource('/links', SocialLinkController::class);
    
    // Mason Category
    Route::resource('/mason-categories', MasonCategoryController::class);

    // Customer Lifting
    Route::resource('/customer-stock', CustomerLiftingController::class);
    Route::get('customer-stock/bulk-upload/show', [CustomerLiftingController::class, 'showBulkUploadForm'])->name('customer-liftings.upload.show');
    Route::post('customer-stock/bulk-upload/show', [CustomerLiftingController::class, 'uploadCsvFile'])->name('customer-liftings.upload.save');
    Route::post('customer-stock/bulk-upload/progress', [CustomerLiftingController::class, 'getProgress'])->name('customer-liftings.upload.progress');
    Route::get('/customer-stock/export/data', [CustomerLiftingController::class, 'customerStockExport'])->name('customer-stock.export');

    //bulk catalogue image upload
    Route::get('catalogues/bulk-image-upload/show', [CatalogueController::class, 'showImagesUploadForm'])->name('catalogue.images.show');
    Route::post('catalogues/bulk-image-upload', [CatalogueController::class, 'uploadImages'])->name('catalogue.images.upload');
    

    // Reports
    Route::get('/reports/mason/points', [ReportController::class, 'masonReports'])->name('mason.points');
    Route::get('/reports/mason/points/export/data', [ReportController::class, 'masonReportsExport'])->name('mason.points.export');

    //Settings
    Route::resource('/settings',App\Http\Controllers\Admin\SettingController::class);

    //Roles
    Route::resource('/roles', App\Http\Controllers\Admin\RoleController::class);

    Route::get('/removing-testing-data', [App\Http\Controllers\Admin\UserController::class, 'removeTestingAccountAndData']);
    Route::get('/updating-customer-lifting-quantity', [App\Http\Controllers\Admin\CustomerLiftingController::class, 'updatingCustomerLiftingQuantity']);

    //Upload rectified dealer data
    Route::get('/upload-rectified-dealer-data', [App\Http\Controllers\Admin\DealerController::class, 'uploadRectifiedCsvFileToUpdateDealers'])->name('dealers.rectify.upload');
    //Adjusting Contractor's (Mason) negative point.
    Route::get('/adjust-negative_point', [App\Http\Controllers\Admin\UserController::class, 'adjustingMasonNegativePoints']);

    Route::get('/send-note', [App\Http\Controllers\Admin\TestController::class, 'sendNote']);

});