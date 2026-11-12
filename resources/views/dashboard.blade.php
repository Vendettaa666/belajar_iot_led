<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring DHT11 - IoT Polije</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .glass {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-slate-900 text-slate-100 min-h-screen" x-data="sensorDashboard('{{ $apiKey }}')" x-init="init()">

    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Sidebar Sederhana -->
        <aside class="w-full md:w-64 glass border-r border-slate-700/50 p-6 flex flex-col">
            <div class="flex items-center gap-3 mb-10">
                <div
                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-lg tracking-wide">IoT Sensor</h1>
                    <p class="text-xs text-slate-400">DHT11 Monitor</p>
                </div>
            </div>

            <div class="mt-auto pt-6 border-t border-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full" :class="isOnline ? 'bg-green-500 animate-pulse' : 'bg-red-500'">
                    </div>
                    <span class="text-sm font-medium" x-text="isOnline ? 'Sensor Online' : 'Sensor Offline'"></span>
                </div>
                <p class="text-xs text-slate-500 mt-2" x-text="lastUpdate"></p>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto">
            <!-- Status Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Temperature Card -->
                <div class="glass rounded-2xl p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-red-500/10 rounded-full -mr-16 -mt-16"></div>
                    <div class="relative">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-12 h-12 rounded-full bg-red-500/20 flex items-center justify-center text-red-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-slate-400 text-sm">Suhu Ruangan</p>
                                <h3 class="text-4xl font-bold text-red-400"><span x-text="latestTemp"></span>°C</h3>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <span>Min: <span class="text-slate-300" x-text="minTemp"></span>°C</span>
                            <span class="mx-2">•</span>
                            <span>Max: <span class="text-slate-300" x-text="maxTemp"></span>°C</span>
                            <span class="mx-2">•</span>
                            <span>Avg: <span class="text-slate-300" x-text="avgTemp"></span>°C</span>
                        </div>
                    </div>
                </div>

                <!-- Humidity Card -->
                <div class="glass rounded-2xl p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full -mr-16 -mt-16"></div>
                    <div class="relative">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-slate-400 text-sm">Kelembapan Udara</p>
                                <h3 class="text-4xl font-bold text-blue-400"><span x-text="latestHum"></span>%</h3>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <span>Min: <span class="text-slate-300" x-text="minHum"></span>%</span>
                            <span class="mx-2">•</span>
                            <span>Max: <span class="text-slate-300" x-text="maxHum"></span>%</span>
                            <span class="mx-2">•</span>
                            <span>Avg: <span class="text-slate-300" x-text="avgHum"></span>%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart -->
            <div class="glass rounded-2xl p-6 mb-8">
                <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                    Grafik Real-time (20 Data Terakhir)
                </h2>
                <div class="relative h-64 md:h-80">
                    <canvas id="sensorChart"></canvas>
                </div>
            </div>

            <!-- Info Box -->
            <div class="glass rounded-2xl p-6">
                <h3 class="font-bold text-lg mb-3 text-cyan-400">ℹ️ Status Kenyamanan Ruangan</h3>
                <p class="text-slate-300" x-text="getComfortStatus()"></p>
            </div>
        </main>
    </div>
    <script>
    function sensorDashboard(apiKey) {
        return {
            apiKey: apiKey,
            latestTemp: '--', latestHum: '--',
            minTemp: '--', maxTemp: '--', avgTemp: '--',
            minHum: '--', maxHum: '--', avgHum: '--',
            isOnline: false, lastUpdate: 'Menunggu data...',
            chart: null,
            chartData: { labels: [], temp: [], hum: [] },

            async init() {
                // HANYA buat chart sekali saat init
                this.initChart();
                await this.fetchData();
                // Update setiap 10 detik
                setInterval(() => this.fetchData(), 10000);
            },

            async fetchData() {
                try {
                    const url = `/api/esp/sensor/latest?api_key=${this.apiKey}`;
                    const response = await fetch(url);

                    if (!response.ok) {
                        console.error('API Error:', response.status);
                        this.isOnline = false;
                        return;
                    }

                    const data = await response.json();
                    console.log('Data dari server:', data);

                    if (data.latest) {
                        this.latestTemp = data.latest.temperature;
                        this.latestHum = data.latest.humidity;
                        this.lastUpdate = 'Update: ' + data.latest.time;

                        // Type checking yang lebih ketat
                        this.isOnline = Boolean(data.is_online);

                        // Tambah data ke chart
                        this.chartData.labels.push(data.latest.time);
                        this.chartData.temp.push(parseFloat(data.latest.temperature));
                        this.chartData.hum.push(parseFloat(data.latest.humidity));

                        // Batasi 20 data
                        if (this.chartData.labels.length > 20) {
                            this.chartData.labels.shift();
                            this.chartData.temp.shift();
                            this.chartData.hum.shift();
                        }

                        this.calculateStats();
                        this.updateChart();

                        console.log('Status online:', this.isOnline);
                    } else {
                        this.isOnline = false;
                        console.log('Tidak ada data latest');
                    }
                } catch (error) {
                    console.error('Gagal fetch data:', error);
                    this.isOnline = false;
                }
            },

            calculateStats() {
                if (this.chartData.temp.length === 0) return;
                const t = this.chartData.temp, h = this.chartData.hum;
                this.minTemp = Math.min(...t).toFixed(1);
                this.maxTemp = Math.max(...t).toFixed(1);
                this.avgTemp = (t.reduce((a, b) => a + b, 0) / t.length).toFixed(1);
                this.minHum = Math.min(...h).toFixed(1);
                this.maxHum = Math.max(...h).toFixed(1);
                this.avgHum = (h.reduce((a, b) => a + b, 0) / h.length).toFixed(1);
            },

            getComfortStatus() {
                const t = parseFloat(this.latestTemp), h = parseFloat(this.latestHum);
                if (isNaN(t) || isNaN(h)) return 'Menunggu data dari sensor...';
                if (t >= 20 && t <= 26 && h >= 40 && h <= 60) return '✅ Nyaman - Kondisi ideal ruangan.';
                if (t > 26) return '🌡️ Terlalu panas - Pertimbangkan untuk menyalakan kipas/AC.';
                if (t < 20) return '❄️ Terlalu dingin.';
                if (h > 60) return '💧 Terlalu lembap - Risiko tumbuhnya jamur.';
                return '🌬️ Udara terlalu kering.';
            },

            initChart() {
                // Hancurkan chart lama jika ada (PENTING!)
                if (this.chart) {
                    this.chart.destroy();
                    this.chart = null;
                }

                const ctx = document.getElementById('sensorChart').getContext('2d');
                this.chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [
                            {
                                label: 'Suhu (°C)',
                                data: [],
                                borderColor: '#f87171',
                                backgroundColor: 'rgba(248, 113, 113, 0.1)',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Kelembapan (%)',
                                data: [],
                                borderColor: '#60a5fa',
                                backgroundColor: 'rgba(96, 165, 250, 0.1)',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true,
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { labels: { color: '#94a3b8' } }
                        },
                        scales: {
                            x: {
                                ticks: { color: '#64748b', maxTicksLimit: 10 },
                                grid: { color: 'rgba(148, 163, 184, 0.1)' }
                            },
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: { display: true, text: 'Suhu (°C)', color: '#f87171' },
                                ticks: { color: '#f87171' },
                                grid: { color: 'rgba(148, 163, 184, 0.1)' }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: { display: true, text: 'Kelembapan (%)', color: '#60a5fa' },
                                ticks: { color: '#60a5fa' },
                                grid: { drawOnChartArea: false }
                            }
                        }
                    }
                });
            },

            updateChart() {
                if (this.chart) {
                    this.chart.data.labels = [...this.chartData.labels];
                    this.chart.data.datasets[0].data = [...this.chartData.temp];
                    this.chart.data.datasets[1].data = [...this.chartData.hum];
                    this.chart.update('none'); // 'none' untuk animasi lebih cepat
                    console.log('Chart updated:', this.chartData.labels.length, 'data points');
                }
            }
        }
    }
</script>
</body>

</html>
