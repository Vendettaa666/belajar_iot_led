<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EspController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Route untuk ESP8266 (Tanpa Sanctum Auth)
Route::prefix('esp')->group(function () {
    // ESP ambil perintah LED
    Route::get('/status', [EspController::class, 'getStatus']);

    // ESP lapor status fisik LED
    Route::post('/report', [EspController::class, 'reportStatus']);
});
