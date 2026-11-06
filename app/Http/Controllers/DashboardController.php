<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Led;
use App\Models\LedLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Menampilkan halaman dashboard
    public function index()
    {
        // Ambil device pertama beserta data leds-nya
        $device = Device::with('leds')->firstOrFail();

        return view('dashboard', compact('device'));
    }
        // Menangani permintaan toggle LED dari frontend
    public function toggleLed(Request $request, $ledId)
    {
        $request->validate([
            'target_on' => 'required|boolean'
        ]);

        $led = Led::findOrFail($ledId);

        // Update perintah target ke database
        $led->update(['target_on' => $request->target_on]);

        // Catat di log bahwa perintah ini berasal dari Web
        LedLog::create([
            'led_id' => $led->id,
            'is_on'  => $request->target_on,
            'source' => 'web'
        ]);

        return response()->json([
            'success' => true,
            'target_on' => $led->target_on,
            'message' => 'Perintah dikirim ke ESP8266'
        ]);
    }
}
