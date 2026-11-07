<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Led;
use App\Models\LedLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $device = Device::with('leds')->firstOrFail();

        // Ambil 10 log aktivitas terbaru
        $recentLogs = LedLog::with('led')
            ->latest()
            ->take(10)
            ->get()
            ->map(function($log) {
                $log->time_ago = $log->created_at->diffForHumans();
                return $log;
            });

        // Statistik sederhana
        $stats = [
            'total_leds' => $device->leds->count(),
            'active_leds' => $device->leds->where('is_on', true)->count(),
            'logs_today' => LedLog::whereDate('created_at', Carbon::today())->count(),
        ];

        return view('dashboard', compact('device', 'recentLogs', 'stats'));
    }

    public function toggleLed(Request $request, $ledId)
    {
        $request->validate(['target_on' => 'required|boolean']);
        $led = Led::findOrFail($ledId);
        $led->update(['target_on' => $request->target_on]);

        LedLog::create([
            'led_id' => $led->id,
            'is_on'  => $request->target_on,
            'source' => 'web'
        ]);

        return response()->json(['success' => true, 'target_on' => $led->target_on]);
    }

    // Fitur Baru: Quick Scenes
    public function applyScene(Request $request)
    {
        $request->validate(['scene' => 'required|string']);
        $scene = $request->scene;
        $device = Device::firstOrFail();
        $leds = $device->leds;

        if ($scene === 'all_on') {
            $leds->each->update(['target_on' => true]);
            $message = "Semua LED dinyalakan";
        } elseif ($scene === 'all_off') {
            $leds->each->update(['target_on' => false]);
            $message = "Semua LED dimatikan";
        } elseif ($scene === 'reading_mode') {
            // Misal: LED 1 & 2 nyala, 3 & 4 mati
            Led::where('number', 1)->update(['target_on' => true]);
            Led::where('number', 2)->update(['target_on' => true]);
            Led::where('number', 3)->update(['target_on' => false]);
            Led::where('number', 4)->update(['target_on' => false]);
            $message = "Mode Baca diaktifkan";
        } elseif ($scene === 'sleep_mode') {
            // Misal: Hanya LED 4 (lampu tidur) nyala redup (atau mati semua)
            $leds->each->update(['target_on' => false]);
            Led::where('number', 4)->update(['target_on' => true]);
            $message = "Mode Tidur diaktifkan";
        }

        // Catat log untuk scene
        LedLog::create([
            'led_id' => $leds->first()->id, // Log ke LED pertama sebagai representasi
            'is_on'  => true,
            'source' => 'scene:' . $scene
        ]);

        return response()->json(['success' => true, 'message' => $message]);
    }
}
