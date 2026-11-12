<?php

namespace App\Http\Controllers;

use App\Models\Device;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil API Key untuk dikirim ke frontend agar bisa fetch data
        $apiKey = Device::first()->api_key ?? '';

        return view('dashboard', compact('apiKey'));
    }
}
