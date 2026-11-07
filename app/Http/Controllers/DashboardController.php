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

        $recentLogs = LedLog::with('led')
            ->latest()
            ->take(10)
            ->get()
            ->map(function($log) {
                $log->time_ago = $log->created_at->diffForHumans();
                return $log;
            });

        $stats = [
            'total_leds' => $device->leds->count(),
            'active_leds' => $device->leds->where('is_on', true)->count(),
            'logs_today' => LedLog::whereDate('created_at', Carbon::today())->count(),
        ];

        return view('dashboard', compact('device', 'recentLogs', 'stats'));
    }

    // Endpoint baru khusus untuk AJAX (JSON only)
    public function getDeviceData()
    {
        $device = Device::with('leds')->firstOrFail();

        $recentLogs = LedLog::with('led')
            ->latest()
            ->take(10)
            ->get()
            ->map(function($log) {
                $log->time_ago = $log->created_at->diffForHumans();
                return $log;
            });

        $stats = [
            'total_leds' => $device->leds->count(),
            'active_leds' => $device->leds->where('is_on', true)->count(),
            'logs_today' => LedLog::whereDate('created_at', Carbon::today())->count(),
        ];

        return response()->json([
            'device' => $device,
            'stats' => $stats,
            'recentLogs' => $recentLogs
        ]);
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

        return response()->json(['success' => true]);
    }

    public function applyScene(Request $request)
    {
        $request->validate(['scene' => 'required|string']);
        $scene = $request->scene;
        $device = Device::firstOrFail();
        $leds = $device->leds;

        if ($scene === 'all_on') {
            $leds->each->update(['target_on' => true]);
        } elseif ($scene === 'all_off') {
            $leds->each->update(['target_on' => false]);
        } elseif ($scene === 'reading_mode') {
            Led::where('number', 1)->update(['target_on' => true]);
            Led::where('number', 2)->update(['target_on' => true]);
            Led::where('number', 3)->update(['target_on' => false]);
            Led::where('number', 4)->update(['target_on' => false]);
        } elseif ($scene === 'sleep_mode') {
            $leds->each->update(['target_on' => false]);
            Led::where('number', 4)->update(['target_on' => true]);
        }

        LedLog::create([
            'led_id' => $leds->first()->id,
            'is_on'  => true,
            'source' => 'scene:' . $scene
        ]);

        return response()->json(['success' => true]);
    }
}
