<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SublocationController;

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

    Route::put('location/{id}', [LocationController::class, 'update'])->name('location.update');
    Route::resource('location', LocationController::class)->except(['destroy']);

    Route::resource('location.sublocation', SublocationController::class)->except(['desctroy']);
});

require __DIR__ . '/auth.php';
