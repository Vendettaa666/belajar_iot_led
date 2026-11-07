<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/data', [DashboardController::class, 'getDeviceData'])->name('dashboard.data'); // Route baru ini
Route::post('/dashboard/led/{led}/toggle', [DashboardController::class, 'toggleLed'])->name('led.toggle');
Route::post('/dashboard/scene', [DashboardController::class, 'applyScene'])->name('apply.scene');

Route::get('/', function () {
    return redirect()->route('dashboard');
});
