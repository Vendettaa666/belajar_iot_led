<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\SensorReading;
use Illuminate\Http\Request;

class EspController extends Controller
{
    /**
     * ESP mengirim data sensor DHT11 (POST)
     */
    public function reportSensor(Request $request)
    {
        $device = $this->authenticateDevice($request);
        if (!$device) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'temperature' => 'required|numeric',
            'humidity'    => 'required|numeric',
        ]);

        $device->update(['last_seen_at' => now()]);

        SensorReading::create([
            'device_id'   => $device->id,
            'temperature' => $request->temperature,
            'humidity'    => $request->humidity,
        ]);

        return response()->json(['message' => 'Data sensor disimpan']);
    }

    /**
     * Dashboard mengambil data sensor terbaru (GET)
     */
    public function getLatestSensor(Request $request)
    {
        $device = $this->authenticateDevice($request);
        if (!$device) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Ambil 20 data terakhir untuk chart (diurutkan dari lama ke baru)
        $readings = $device->readings()
            ->latest()
            ->take(20)
            ->get()
            ->reverse()
            ->map(function($r) {
                $r->time = $r->created_at->format('H:i:s');
                return $r;
            });

        $latest = $device->readings()->latest()->first();

        return response()->json([
            'device_name' => $device->name,
            'is_online'   => $device->last_seen_at && $device->last_seen_at->gt(now()->subSeconds(60)),
            'latest'      => $latest ? [
                'temperature' => (float) $latest->temperature,
                'humidity'    => (float) $latest->humidity,
                'time'        => $latest->created_at->format('d M Y, H:i:s'),
            ] : null,
            'history'     => $readings,
        ]);
    }

    private function authenticateDevice(Request $request)
    {
        $apiKey = $request->header('X-API-KEY') ?? $request->input('api_key');
        if (!$apiKey) return null;
        return Device::where('api_key', $apiKey)->first();
    }
}
