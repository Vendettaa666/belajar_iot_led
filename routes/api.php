<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EspController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Route khusus untuk ESP8266 mengirim & mengambil data DHT11
Route::prefix('esp')->group(function () {

    // 1. ESP mengirim data suhu & kelembapan ke server
    Route::post('/sensor', [EspController::class, 'reportSensor']);

    // 2. Dashboard mengambil data terbaru untuk ditampilkan di grafik
    Route::get('/sensor/latest', [EspController::class, 'getLatestSensor']);

});
