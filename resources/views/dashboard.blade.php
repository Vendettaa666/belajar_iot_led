<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard IoT ESP8266 - Polije</title>
    <!-- Tailwind CSS (via CDN untuk kemudahan praktikum) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js untuk interaktivitas tanpa reload -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-4 md:p-8">

    <div class="max-w-4xl mx-auto"
         x-data="iotDashboard({{ $device->toJson() }})"
         x-init="startAutoRefresh()">

        <!-- Header -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800" x-text="device.name"></h1>
                <p class="text-sm text-gray-500 mt-1">API Key: <span class="font-mono text-xs bg-gray-200 px-2 py-1 rounded" x-text="device.api_key"></span></p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-sm font-semibold flex items-center gap-2"
                      :class="device.is_online ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                    <span class="w-2 h-2 rounded-full" :class="device.is_online ? 'bg-green-500 animate-pulse' : 'bg-red-500'"></span>
                    <span x-text="device.is_online ? 'Online' : 'Offline'"></span>
                </span>
                <button @click="fetchData()" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :class="{'animate-spin': loading}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
            </div>
        </div>

        <!-- Grid LED Controls -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <template x-for="led in device.leds" :key="led.id">
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 transition-all duration-300"
                     :class="led.is_on ? 'border-yellow-400 bg-yellow-50' : 'border-gray-300'">

                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800" x-text="led.name"></h3>
                            <p class="text-xs text-gray-500">GPIO Pin: <span class="font-mono font-semibold" x-text="led.gpio_pin"></span></p>
                        </div>
                        <!-- Indikator Visual LED -->
                        <div class="w-8 h-8 rounded-full shadow-inner transition-all duration-300"
                             :class="led.is_on ? 'bg-yellow-400 shadow-yellow-200' : 'bg-gray-300'">
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <span class="text-sm font-medium text-gray-600" x-text="led.is_on ? 'Menyala 💡' : 'Mati ⚫'"></span>

                        <!-- Toggle Switch -->
                        <button @click="toggleLed(led)"
                                :disabled="loading"
                                class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                :class="led.target_on ? 'bg-blue-600' : 'bg-gray-300'">
                            <span class="sr-only">Toggle LED</span>
                            <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform shadow-sm"
                                  :class="led.target_on ? 'translate-x-6' : 'translate-x-1'">
                            </span>
                        </button>
                    </div>

                    <p class="text-xs text-gray-400 mt-3 text-center" x-show="led.target_on !== led.is_on">
                        ⏳ Menunggu respon ESP8266...
                    </p>
                </div>
            </template>
        </div>

        <!-- Footer / Info -->
        <!-- Ubah bagian footer ini -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>Workshop Pemrograman Dasar - Politeknik Negeri Jember</p>
            <p class="text-xs mt-1">Pastikan ESP8266 terhubung ke internet untuk sinkronisasi real-time.</p>
        </div>
    </div>

    <script>
        function iotDashboard(initialData) {
            return {
                device: initialData,
                loading: false,

                // Fungsi untuk mengambil data terbaru dari server
                async fetchData() {
                    this.loading = true;
                    try {
                        // Kita ambil ulang data device dari endpoint index (bisa dioptimalkan dengan API endpoint khusus)
                        const response = await fetch(window.location.href);
                        const html = await response.text();

                        // Parse HTML untuk mengambil data JSON yang di-inject Blade
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const scriptContent = doc.querySelector('div[x-data]').getAttribute('x-data');

                        // Extract JSON dari string x-data="iotDashboard({...})"
                        const jsonString = scriptContent.match(/\{[\s\S]*\}/)[0];
                        this.device = JSON.parse(jsonString);
                    } catch (error) {
                        console.error('Gagal mengambil data:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                // Fungsi saat tombol toggle diklik
                async toggleLed(led) {
                    // Optimistic UI: Ubah tampilan langsung agar terasa responsif
                    const previousTarget = led.target_on;
                    led.target_on = !previousTarget;

                    this.loading = true;
                    try {
                        const response = await fetch(`/dashboard/led/${led.id}/toggle`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ target_on: led.target_on })
                        });

                        const result = await response.json();
                        if (!result.success) {
                            // Kembalikan ke keadaan semula jika gagal
                            led.target_on = previousTarget;
                            alert('Gagal mengirim perintah!');
                        }
                    } catch (error) {
                        led.target_on = previousTarget;
                        alert('Koneksi error!');
                    } finally {
                        this.loading = false;
                    }
                },

                // Auto refresh setiap 3 detik untuk mengecek status 'is_on' dari ESP
                startAutoRefresh() {
                    setInterval(() => {
                        this.fetchData();
                    }, 3000);
                }
            }
        }
    </script>

    <!-- Meta tag untuk CSRF (dibutuhkan Laravel) -->
    <!-- Kita inject manual karena ini file blade standalone -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</body>
</html>
