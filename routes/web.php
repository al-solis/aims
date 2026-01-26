<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SublocationController;
use App\Http\Controllers\EmployeeController;

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
});

require __DIR__ . '/auth.php';
