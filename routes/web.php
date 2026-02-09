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

    Route::get('/main', function () {
        return view('main');
    })->name('main');

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

    Route::get('/get-sublocations/{location}', [LocationController::class, 'getSublocations']);
    Route::get('/asset/labels', [AssetController::class, 'printAssetLabel'])->name('asset.labels');
    Route::post('/assets/print-labels', [AssetController::class, 'printAssetLabel'])->name('assets.print.labels');

    Route::post('/asset/save-selection', [AssetController::class, 'saveSelection'])->name('asset.save-selection');
    Route::post('/asset/clear-selection', [AssetController::class, 'clearSelection'])->name('asset.clear-selection');

    Route::resource('asset', AssetController::class)->except(['destroy']);

    Route::get('asset/transfer/{assetId}/count', [TransferController::class, 'countAssetTransfers'])->name('asset.transfer.count');
    Route::resource('asset/transfer', TransferController::class)->except(['destroy']);

    Route::get('/validate-transfer-date', [TransferController::class, 'validateTransferDate'])->name('asset.transfer.validate-date');
    Route::get('/get-last-transfer-code/{assetId}', [TransferController::class, 'getLastTransferCode'])->name('asset.transfer.last-code');
    Route::post('/transfer/{transferId}/void', [TransferController::class, 'voidTransfer'])->name('asset.transfer.void');
});

require __DIR__ . '/auth.php';

