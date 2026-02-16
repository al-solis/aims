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

    Route::resource('setup/idtype', IdTypeController::class)->except(['destroy']);

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


    Route::get('/get-sublocations/{location}', [LocationController::class, 'getSublocations']);
    Route::get('/asset/labels', [AssetController::class, 'printAssetLabel'])->name('asset.labels');
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

    Route::get('/licenses', [AssetLicenseController::class, 'index'])->name('licenses.index');
    Route::post('/licenses', [AssetLicenseController::class, 'store'])->name('licenses.store');
    Route::put('/licenses/{id}', [AssetLicenseController::class, 'update'])->name('licenses.update');
    //Route::resource('licenses', AssetLicenseController::class)->except(['destroy']);

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::resource('clearance', ClearanceHeaderController::class)->except(['destroy']);
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
});

require __DIR__ . '/auth.php';

