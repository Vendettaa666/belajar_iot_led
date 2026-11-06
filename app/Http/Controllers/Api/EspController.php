<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Led;
use App\Models\LedLog;
use Illuminate\Http\Request;

class EspController extends Controller
{
    /**
     * 1. ESP mengambil status target LED (GET)
     * URL: /api/esp/status
     */
    public function getStatus(Request $request)
    {
        $device = $this->authenticateDevice($request);
        if (!$device) {
            return response()->json(['message' => 'Unauthorized: API Key tidak valid'], 401);
        }

        // Update waktu terakhir ESP terhubung
        $device->update(['last_seen_at' => now()]);

        // Ambil semua LED milik device ini
        $leds = $device->leds->map(function ($led) {
            return [
                'number'    => $led->number,
                'gpio_pin'  => $led->gpio_pin,
                'target_on' => (bool) $led->target_on, // Perintah dari web
                'is_on'     => (bool) $led->is_on,     // Status aktual terakhir
            ];
        });

        return response()->json([
            'device_name' => $device->name,
            'leds'        => $leds
        ]);
    }

    /**
     * 2. ESP melapor status aktual LED (POST)
     * URL: /api/esp/report
     */
    public function reportStatus(Request $request)
    {
        $device = $this->authenticateDevice($request);
        if (!$device) {
            return response()->json(['message' => 'Unauthorized: API Key tidak valid'], 401);
        }

        // Validasi dasar (is_on kita terima apa saja, nanti di-cast manual karena ESP sering kirim 1/0)
        $request->validate([
            'leds' => 'required|array',
            'leds.*.number' => 'required|integer',
            'leds.*.is_on'  => 'required',
        ]);

        // Update last seen
        $device->update(['last_seen_at' => now()]);

        // Proses setiap laporan LED
        foreach ($request->leds as $data) {
            $led = Led::where('device_id', $device->id)
                      ->where('number', $data['number'])
                      ->first();

            if ($led) {
                $previousState = (bool) $led->is_on;
                // Cast manual agar aman menerima 1, 0, true, false dari ESP
                $newState = filter_var($data['is_on'], FILTER_VALIDATE_BOOLEAN);

                // Update status aktual di database
                $led->update(['is_on' => $newState]);

                // Catat ke led_logs HANYA jika ada perubahan status
                if ($previousState !== $newState) {
                    LedLog::create([
                        'led_id' => $led->id,
                        'is_on'  => $newState,
                        'source' => 'device', // Sumber laporan dari ESP
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Laporan berhasil diterima']);
    }

    /**
     * Helper: Autentikasi device berdasarkan api_key
     */
    private function authenticateDevice(Request $request)
    {
        // Ambil api_key dari Header 'X-API-KEY' atau dari body/query 'api_key'
        $apiKey = $request->header('X-API-KEY') ?? $request->input('api_key');

        if (!$apiKey) {
            return null;
        }

        return Device::where('api_key', $apiKey)->first();
    }
}
