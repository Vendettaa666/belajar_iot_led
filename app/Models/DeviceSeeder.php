<?php

namespace Database\Seeders;

use App\Models\Device;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $device = Device::create([
            'name'    => 'ESP8266 WeMos D1 Mini',
            'api_key' => Str::random(40),
        ]);

        // Pin sesuai rangkaianmu yang sudah jalan: 2, 0, 4, 5
        $pins = [2, 0, 4, 5];

        foreach ($pins as $i => $pin) {
            $device->leds()->create([
                'number'   => $i + 1,
                'name'     => 'LED ' . ($i + 1),
                'gpio_pin' => $pin,
            ]);
        }

        // Catat API key ini, nanti dipakai di kode ESP8266
        $this->command->info('API KEY: ' . $device->api_key);
    }
}
