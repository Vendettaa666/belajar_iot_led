<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart IoT Dashboard - Polije</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .glass {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .glow-yellow { box-shadow: 0 0 20px rgba(250, 204, 21, 0.4); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen"
      x-data='iotDashboard(@json($device), @json($recentLogs), @json($stats))'
      x-init="startAutoRefresh()">

    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Sidebar -->
        <aside class="w-full md:w-64 glass border-r border-slate-700/50 p-6 flex flex-col">
            <div class="flex items-center gap-3 mb-10">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h1 class="font-bold text-lg tracking-wide">ESP8266 IoT</h1>
                    <p class="text-xs text-slate-400">Smart Home v2.0</p>
                </div>
            </div>

            <nav class="space-y-2 flex-1">
                <div class="pt-4 pb-2 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Quick Scenes</div>
                <button @click="applyScene('all_on')" class="w-full flex items-center gap-3 px-4 py-3 text-slate-300 hover:bg-slate-800/50 rounded-xl transition">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Semua Nyala
                </button>
                <button @click="applyScene('all_off')" class="w-full flex items-center gap-3 px-4 py-3 text-slate-300 hover:bg-slate-800/50 rounded-xl transition">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    Semua Mati
                </button>
                <button @click="applyScene('reading_mode')" class="w-full flex items-center gap-3 px-4 py-3 text-slate-300 hover:bg-slate-800/50 rounded-xl transition">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Mode Baca
                </button>
                <button @click="applyScene('sleep_mode')" class="w-full flex items-center gap-3 px-4 py-3 text-slate-300 hover:bg-slate-800/50 rounded-xl transition">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    Mode Tidur
                </button>
            </nav>

            <div class="mt-auto pt-6 border-t border-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full" :class="device.is_online ? 'bg-green-500 animate-pulse' : 'bg-red-500'"></div>
                    <span class="text-sm font-medium" x-text="device.is_online ? 'Perangkat Online' : 'Perangkat Offline'"></span>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto">
            <!-- Header Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="glass rounded-2xl p-6 flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm mb-1">Total LED</p>
                        <h3 class="text-3xl font-bold" x-text="stats.total_leds"></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    </div>
                </div>
                <div class="glass rounded-2xl p-6 flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm mb-1">Sedang Aktif</p>
                        <h3 class="text-3xl font-bold text-yellow-400" x-text="stats.active_leds"></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-yellow-500/20 flex items-center justify-center text-yellow-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
                <div class="glass rounded-2xl p-6 flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm mb-1">Log Hari Ini</p>
                        <h3 class="text-3xl font-bold text-cyan-400" x-text="stats.logs_today"></h3>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-cyan-500/20 flex items-center justify-center text-cyan-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                </div>
            </div>

            <!-- LED Grid -->
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Kontrol Perangkat
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <template x-for="led in device.leds" :key="led.id">
                    <div class="glass rounded-2xl p-5 transition-all duration-300 relative overflow-hidden"
                         :class="led.is_on ? 'border-yellow-500/30 glow-yellow' : 'border-slate-700/50'">

                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="font-bold text-lg" x-text="led.name"></h3>
                                <p class="text-xs text-slate-400 mt-1">GPIO <span class="font-mono text-slate-300" x-text="led.gpio_pin"></span></p>
                            </div>
                            <div class="w-10 h-10 rounded-full transition-all duration-300 flex items-center justify-center"
                                 :class="led.is_on ? 'bg-yellow-400 text-slate-900 glow-yellow' : 'bg-slate-800 text-slate-600'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium" :class="led.is_on ? 'text-yellow-400' : 'text-slate-500'" x-text="led.is_on ? 'Menyala' : 'Mati'"></span>

                            <button @click="toggleLed(led)" :disabled="loading"
                                    class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors focus:outline-none"
                                    :class="led.target_on ? 'bg-blue-600' : 'bg-slate-700'">
                                <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform shadow-sm"
                                      :class="led.target_on ? 'translate-x-6' : 'translate-x-1'"></span>
                            </button>
                        </div>

                        <div x-show="led.target_on !== led.is_on" x-transition class="absolute bottom-0 left-0 right-0 bg-blue-600/20 text-blue-400 text-[10px] text-center py-1 backdrop-blur-sm">
                            ⏳ Sinkronisasi...
                        </div>
                    </div>
                </template>
            </div>

            <!-- Activity Log -->
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Riwayat Aktivitas Terkini
            </h2>
            <div class="glass rounded-2xl p-6">
                <div class="space-y-4">
                    <template x-for="log in recentLogs" :key="log.id">
                        <div class="flex items-center gap-4 pb-4 border-b border-slate-700/50 last:border-0 last:pb-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                                 :class="log.is_on ? 'bg-yellow-500/20 text-yellow-400' : 'bg-slate-700/50 text-slate-400'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">
                                    <span class="text-slate-200" x-text="log.led.name"></span>
                                    <span class="mx-2 text-slate-600">•</span>
                                    <span :class="log.is_on ? 'text-yellow-400' : 'text-slate-400'" x-text="log.is_on ? 'Dinyalakan' : 'Dimatikan'"></span>
                                </p>
                                <p class="text-xs text-slate-500 mt-1">
                                    Sumber: <span class="uppercase font-mono" x-text="log.source"></span>
                                </p>
                            </div>
                            <div class="text-xs text-slate-400 text-right" x-text="log.time_ago"></div>
                        </div>
                    </template>
                </div>
            </div>
        </main>
    </div>

    <script>
        function iotDashboard(initialDevice, initialLogs, initialStats) {
            return {
                device: initialDevice,
                recentLogs: initialLogs,
                stats: initialStats,
                loading: false,

                // FUNGSI AJAX BARU: Update tanpa reload halaman
                async fetchData() {
                    try {
                        const response = await fetch('{{ route("dashboard.data") }}');
                        const data = await response.json();

                        this.device = data.device;
                        this.stats = data.stats;
                        this.recentLogs = data.recentLogs;
                    } catch (error) {
                        console.error('Gagal update data:', error);
                    }
                },

                async toggleLed(led) {
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
                        if (!result.success) led.target_on = previousTarget;
                    } catch (error) {
                        led.target_on = previousTarget;
                    } finally {
                        this.loading = false;
                    }
                },

                async applyScene(sceneName) {
                    this.loading = true;
                    try {
                        const response = await fetch('/dashboard/scene', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ scene: sceneName })
                        });
                        const result = await response.json();
                        if(result.success) {
                            // Langsung update data setelah scene diterapkan
                            await this.fetchData();
                        }
                    } catch (error) {
                        console.error('Scene error:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                startAutoRefresh() {
                    // Update UI setiap 1.5 detik secara mulus (tanpa reload)
                    setInterval(() => {
                        this.fetchData();
                    }, 1500);
                }
            }
        }
    </script>
</body>
</html>
