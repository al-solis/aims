<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SublocationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LicenseTypeController;
use App\Http\Controllers\IdTypeController;
use App\Http\Controllers\EmployeeIdController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\AssetLicenseController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ClearanceHeaderController;
use App\Http\Controllers\ClearanceDetailController;
use App\Http\Controllers\EmployeeHistoryController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\OdometerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SuppliesCategoryController;
use App\Http\Controllers\UomController;
use App\Http\Controllers\SuppliesController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReceivingController;
use App\Http\Controllers\IssuanceController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UploadedFileController;
use App\Http\Controllers\DdoHeaderController;
use App\Http\Controllers\ClearanceRoutingController;
use App\Http\Controllers\TransmittalController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\BudgetRoutingController;
use App\Http\Controllers\DocumentTypeController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/main', [MainController::class, 'index'])->name('main');

    Route::put('setup/location/{id}', [LocationController::class, 'update'])->name('location.update');
    Route::resource('setup/location', LocationController::class)->except(['destroy']);

    Route::put('setup/location/sublocation/{id}', [SublocationController::class, 'update'])->name('location.sublocation.update');

    Route::get('setup/location/sublocation/{location}', [SublocationController::class, 'index'])->name('location.sublocation.index');
    Route::post('setup/location/sublocation/{location}', [SublocationController::class, 'store'])->name('location.sublocation.store');
    //Route::resource('setup/location/sublocation', SublocationController::class)->except(['destroy']);

    Route::post('employee/upload-image', [EmployeeController::class, 'uploadImage'])->name('employee.uploadImage');
    Route::post('employee/check-idno', [EmployeeController::class, 'checkIdNo'])->name('employee.checkIdNo');
    Route::post('employee/cleanup-temp-file', [EmployeeController::class, 'cleanupTempFile'])->name('employee.cleanupTempFile');
    Route::resource('employee', EmployeeController::class)->except(['destroy']);

    Route::get('setup', function () {
        return view('setup.index');
    })->name('setup.index');

    Route::resource('setup/category', CategoryController::class)->except(['destroy']);
    Route::resource('setup/license', LicenseTypeController::class)->except(['destroy']);
    Route::resource('setup/doctype', DocumentTypeController::class)->except(['destroy']);
    Route::resource('setup/idtype', IdTypeController::class)->except(['destroy']);
    Route::resource('setup/supplies-category', SuppliesCategoryController::class)->except(['destroy']);
    Route::resource('setup/uom', UomController::class)->except(['destroy']);
    Route::resource('setup/supplier', SupplierController::class)->except(['destroy']);
    Route::resource('setup/user', RegisteredUserController::class)->except(['destroy']);
    Route::resource('setup/ddo', DdoHeaderController::class)->except(['destroy', 'update']);
    Route::get('setup/clearance-routing', [ClearanceRoutingController::class, 'index'])->name('clearance-routing.index');
    Route::get('setup/clearance-routing/list', [ClearanceRoutingController::class, 'viewRouting']);
    Route::post('setup/clearance-routing', [ClearanceRoutingController::class, 'store']);
    Route::put('setup/clearance-routing/{id}', [ClearanceRoutingController::class, 'update']);
    Route::delete('setup/clearance-routing/{id}', [ClearanceRoutingController::class, 'destroy']);
    Route::put('setup/ddo/{ddo}', [DdoHeaderController::class, 'update'])->name('ddo.update');
    Route::get('/ddo/get-employees-by-location', [DdoHeaderController::class, 'getEmployeesByLocation']);
    Route::post('ddo/location/copy', [DdoHeaderController::class, 'copyLocationSetup'])->name('ddo.location.copy');
    Route::get('setup/budget-routing', [BudgetRoutingController::class, 'index'])->name('budget-routing.index');
    Route::get('setup/budget-routing/list', [BudgetRoutingController::class, 'viewRouting']);
    Route::post('setup/budget-routing', [BudgetRoutingController::class, 'store']);
    Route::put('setup/budget-routing/{id}', [BudgetRoutingController::class, 'update']);
    Route::delete('setup/budget-routing/{id}', [BudgetRoutingController::class, 'destroy']);
    Route::get('/budget/{id}/approval-history', [BudgetController::class, 'getApprovalHistory'])->name('budget.approval-history');
    // Route::post('/employee/{employee}/ids', [EmployeeIdController::class, 'store'])
    //     ->name('employee.ids.store');

    // Employee ID routes
    Route::prefix('employee/{employee}/ids')->group(function () {
        Route::get('/', [EmployeeController::class, 'getEmployeeIds'])->name('employee.ids.index');
        Route::post('/', [EmployeeController::class, 'storeEmployeeId'])->name('employee.ids.store');
        Route::get('/{id}', [EmployeeController::class, 'getEmployeeId'])->name('employee.ids.show');
        Route::put('/{id}', [EmployeeController::class, 'updateEmployeeId'])->name('employee.ids.update');
        Route::delete('/{id}', [EmployeeController::class, 'destroyEmployeeId'])->name('employee.ids.destroy');
    });
    Route::get('employee/{id}/accountability', [EmployeeController::class, 'viewAccountability'])->name('employee.accountability');

    // Route::get('employee/{id}/history', [EmployeeHistoryController::class, 'viewHistory'])->name('employee.history');
    Route::get('/employee/{id}/history', [EmployeeHistoryController::class, 'getByEmployee']);
    Route::get('/employee-history/{id}', [EmployeeHistoryController::class, 'show']);

    Route::post('/employee-history/store', [EmployeeHistoryController::class, 'store']);
    Route::put('/employee-history/{id}', [EmployeeHistoryController::class, 'update']);
    Route::delete('/employee-history/{id}', [EmployeeHistoryController::class, 'destroy']);

    Route::post('/employee/{empId}/upload', [UploadedFileController::class, 'uploadFiles'])->name('employee.upload.files');
    Route::get('/employee/{empId}/uploaded-files', [UploadedFileController::class, 'getFiles'])->name('employee.files');

    Route::get('/get-sublocations/{location}', [LocationController::class, 'getSublocations']);
    Route::get('/asset/labels', [AssetController::class, 'printAssetLabel'])->name('asset.labels');
    Route::get('/asset/qr-code', [AssetController::class, 'generateQRCode'])->name('asset.qr-code');

    Route::post('/assets/print-labels', [AssetController::class, 'printAssetLabel'])->name('assets.print.labels');

    Route::post('/asset/save-selection', [AssetController::class, 'saveSelection'])->name('asset.save-selection');
    Route::post('/asset/clear-selection', [AssetController::class, 'clearSelection'])->name('asset.clear-selection');

    Route::resource('asset', AssetController::class)->except(['destroy']);
    Route::post('/asset/{id}/retire', [AssetController::class, 'retire'])->name('asset.retire');

    Route::get('asset/transfer/{assetId}/count', [TransferController::class, 'countAssetTransfers'])->name('asset.transfer.count');
    Route::resource('asset/transfer', TransferController::class)->except(['destroy']);

    Route::match(['put', 'patch'], '/asset/odometer/{id}', [OdometerController::class, 'update'])->name('asset.odometer.update');
    Route::post('/asset/odometer/store', [OdometerController::class, 'store']);
    Route::get('/asset/{assetId}/odometer', [OdometerController::class, 'show'])->name('asset.odometer.show');
    Route::get('/asset/{assetId}/odometer-readings', [OdometerController::class, 'getOdometerReadings'])->name('asset.odometer.readings');
    Route::delete('/asset/odometer/{id}', [OdometerController::class, 'destroy'])->name('asset.odometer.destroy');
    Route::get('/asset/odometer/{id}', [OdometerController::class, 'getReading'])->name('asset.odometer.get');
    Route::get('/asset/{assetId}/odometer/print', [OdometerController::class, 'printOdometer'])->name('asset.odometer.print');

    Route::get('/validate-transfer-date', [TransferController::class, 'validateTransferDate'])->name('asset.transfer.validate-date');
    Route::get('/get-last-transfer-code/{assetId}', [TransferController::class, 'getLastTransferCode'])->name('asset.transfer.last-code');
    Route::post('/transfer/{transferId}/void', [TransferController::class, 'voidTransfer'])->name('asset.transfer.void');
    Route::put('/asset/transfer/update/{id}', [TransferController::class, 'update'])->name('asset.transfer.update');


    Route::get('/receiving/{id}', [ReceivingController::class, 'show']);
    Route::get('/receiving/{id}/print', [ReceivingController::class, 'print'])->name('receiving.print');
    Route::post('/receiving/{id}/void', [ReceivingController::class, 'void'])->name('receiving.void');

    Route::get('/issuance/create', [IssuanceController::class, 'create'])->name('issuance.create');
    Route::get('/issuance/{id}', [IssuanceController::class, 'show']);
    Route::get('/issuance/{id}/print', [IssuanceController::class, 'print'])->name('issuance.print');
    Route::get('/issuance/{id}/print-transmittal', [IssuanceController::class, 'printTransmittal'])->name('issuance.print-transmittal');
    Route::post('/issuance/{id}/void', [IssuanceController::class, 'void'])->name('issuance.void');
    Route::get('/issuance', [IssuanceController::class, 'index'])->name('issuance.index');

    Route::post('/issuance', [IssuanceController::class, 'store'])->name('issuance.store');

    Route::resource('/supplies/receiving', ReceivingController::class)->except(['destroy']);
    Route::post('/supplies/{id}', [SuppliesController::class, 'update'])->name('supplies.update');

    Route::resource('supplies', SuppliesController::class)->except(['destroy']);


    Route::get('/asset-licenses', [AssetLicenseController::class, 'index'])->name('asset-licenses.index');
    Route::post('/asset-licenses', [AssetLicenseController::class, 'store'])->name('asset-licenses.store');
    Route::put('/asset-licenses/{id}', [AssetLicenseController::class, 'update'])->name('asset-licenses.update');
    //Route::resource('licenses', AssetLicenseController::class)->except(['destroy']);

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::resource('clearance', ClearanceHeaderController::class)->except(['destroy']);

    Route::post('/clearance/{id}/submit', [ClearanceHeaderController::class, 'submitForApproval'])->name('clearance.submit');
    Route::post('/clearance/{id}/approve', [ClearanceHeaderController::class, 'approveClearance'])->name('clearance.approve');
    Route::post('/clearance/{id}/reject', [ClearanceHeaderController::class, 'rejectClearance'])->name('clearance.reject');
    Route::put('/clearance/{id}/details', [ClearanceHeaderController::class, 'updateDetails'])
        ->name('clearance.update-details');

    Route::post('/clearance/{id}/mark-complete', [ClearanceHeaderController::class, 'markAsComplete'])->name('clearance.mark-complete');
    Route::post('/clearance/{id}/void', [ClearanceHeaderController::class, 'voidClearance'])->name('clearance.void');
    Route::get(
        '/clearance/{id}/print',
        [ClearanceHeaderController::class, 'print']
    )->name('clearance.print');

    Route::get('/transfer/{id}/print', [TransferController::class, 'print'])->name('transfer.print');
    Route::get('/are/print', [AssetController::class, 'printARE'])->name('are.print');
    Route::get('/duty-detail/print', [AssetController::class, 'printDutyDetail'])->name('duty.detail.print');

    Route::resource('maintenance', MaintenanceController::class)->except(['destroy']);
    Route::post('/maintenance/{id}/mark-complete', [MaintenanceController::class, 'markAsComplete'])->name('maintenance.mark-complete');
    Route::post('/maintenance/{id}/mark-in-progress', [MaintenanceController::class, 'markAsInProgress'])->name('maintenance.mark-in-progress');
    Route::post('/maintenance/{id}/void', [MaintenanceController::class, 'voidMaintenance'])->name('maintenance.void');

    // web.php    
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/asset-summary', [ReportController::class, 'assetSummary'])->name('asset.summary');
        Route::get('/odometer', [ReportController::class, 'odometerReport'])->name('odometer');
        Route::get('/maintenance', [ReportController::class, 'maintenanceReport'])->name('maintenance');
        Route::get('/employee', [ReportController::class, 'employeeReport'])->name('employee');
        Route::get('/supplies-summary', [ReportController::class, 'suppliesReport'])->name('supplies.summary');
        Route::get('/supplies-receiving', [ReportController::class, 'suppliesReceivingReport'])->name('supplies.receiving');
        Route::get('/supplies-issuance', [ReportController::class, 'suppliesIssuanceReport'])->name('supplies.issuance');
        Route::get('/duty-detail-order', [ReportController::class, 'dutyDetailOrderReport'])->name('duty-detail-order');
        Route::get('/budget-request', [ReportController::class, 'budgetRequestReport'])->name('budget.request');
    });

    Route::get('/transmittal', [TransmittalController::class, 'index'])->name('transmittal.index');
    Route::get('/transmittal/create', [TransmittalController::class, 'create'])->name('transmittal.create');
    Route::get('/transmittal/{id}', [TransmittalController::class, 'show'])->name('transmittal.show');
    Route::post('/transmittal/store', [TransmittalController::class, 'store'])->name('transmittal.store');
    Route::get('/get-assets/{locationId}', [TransmittalController::class, 'getAssets']);
    Route::post('/transmittal/{id}/void', [TransmittalController::class, 'voidTransmittal'])->name('transmittal.void');
    Route::get('/transmittal/{id}/print', [TransmittalController::class, 'printTransmittal'])->name('asset.print-transmittal');
    Route::get('/get-transmittal-items/{transmittalId}', [TransmittalController::class, 'getTransmittalItems'])->name('transmittal.items');
    Route::put('/transmittal/{id}', [TransmittalController::class, 'update'])->name('transmittal.update');

    Route::get('/budget', [BudgetController::class, 'index'])->name('budget.index');
    Route::get('/budget/create', [BudgetController::class, 'create'])->name('budget.create');
    Route::get('/budget/{id}', [BudgetController::class, 'show'])->name('budget.show');
    Route::post('/budget/store', [BudgetController::class, 'store'])->name('budget.store');
    Route::put('/budget/{id}', [BudgetController::class, 'update'])->name('budget.update');
    Route::post('/budget/{id}/submit', [BudgetController::class, 'submitForApproval'])->name('budget.submit');
    Route::post('/budget/{id}/approve', [BudgetController::class, 'approveRequest'])->name('budget.approve');
    Route::post('/budget/{id}/reject', [BudgetController::class, 'rejectRequest'])->name('budget.reject');
    Route::post('/budget/{id}/void', [BudgetController::class, 'voidBudget'])->name('budget.void');
    Route::get('/budget/{id}/print', [BudgetController::class, 'printBudget'])->name('budget.print');
});

require __DIR__ . '/auth.php';

