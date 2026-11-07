<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/dashboard/led/{led}/toggle', [DashboardController::class, 'toggleLed'])->name('led.toggle');
Route::post('/dashboard/scene', [DashboardController::class, 'applyScene'])->name('apply.scene');

Route::get('/', function () {
    return redirect()->route('dashboard');
});
