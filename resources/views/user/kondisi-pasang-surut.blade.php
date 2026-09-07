@extends('layouts.app')

@section('title', 'Kondisi Pasang Surut')

@section('content')
<div class="space-y-6">

    <!-- Top Filter & Title Section -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 text-xs font-semibold text-sky-600 mb-1">
                <span>Grafik Fluktuasi Muka Air Laut</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Kondisi Pasang Surut Air Laut</h1>
            <p class="text-xs text-slate-500 mt-0.5">Analisis kurva 24 jam dan tren bulanan per stasiun pengamatan</p>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('user.kondisi') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div>
                <label for="location_id" class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Pilih Lokasi Stasiun</label>
                <select name="location_id" id="location_id" onchange="this.form.submit()"
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold bg-slate-50 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ $selectedLocation->id == $loc->id ? 'selected' : '' }}>
                            {{ $loc->name }} ({{ $loc->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="date" class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Tanggal Pengamatan</label>
                <input type="date" name="date" id="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                       class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold bg-slate-50 focus:ring-2 focus:ring-sky-500 focus:outline-none">
            </div>

            <div class="self-end">
                <button type="submit" class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs shadow-sm transition">
                    Terapkan
                </button>
            </div>
        </form>
    </div>

    <!-- Info Stasiun & Highlight Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Lokasi Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Stasiun Terpilih</span>
            <h3 class="text-base font-extrabold text-slate-900 mt-1">{{ $selectedLocation->name }}</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">{{ $selectedLocation->institution }}</p>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-400">Koordinat</span>
                <span class="font-mono text-[11px] font-semibold text-slate-700">{{ $selectedLocation->latitude }}, {{ $selectedLocation->longitude }}</span>
            </div>
        </div>

        <!-- HHW (Pasang Tertinggi) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pasang Maksimum (HHW)</span>
                <span class="w-2 h-2 rounded-full bg-sky-500"></span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-sky-600">
                    {{ $hhwRecord?->water_level > 0 ? '+' : '' }}{{ $hhwRecord?->water_level ?? 0 }}
                </span>
                <span class="text-xs font-semibold text-slate-500">cm (MSL)</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Terjadi pada: <strong class="text-slate-700">Jam {{ sprintf('%02d:00', $hhwRecord?->hour ?? 0) }}</strong></p>
        </div>

        <!-- LLW (Surut Terendah) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Surut Minimum (LLW)</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-emerald-600">
                    {{ $llwRecord?->water_level ?? 0 }}
                </span>
                <span class="text-xs font-semibold text-slate-500">cm (MSL)</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Terjadi pada: <strong class="text-slate-700">Jam {{ sprintf('%02d:00', $llwRecord?->hour ?? 0) }}</strong></p>
        </div>

        <!-- Rata-rata Muka Air -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rata-Rata Air Harian</span>
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-indigo-600">
                    {{ $avgLevel > 0 ? '+' : '' }}{{ $avgLevel }}
                </span>
                <span class="text-xs font-semibold text-slate-500">cm</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Tanggal: <strong class="text-slate-700">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</strong></p>
        </div>
    </div>

    <!-- Main Chart: 24 Jam Pasang Surut ApexCharts -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
            <div>
                <h2 class="text-base sm:text-lg font-extrabold text-slate-900">Kurva Fluktuasi Pasang Surut (24 Jam)</h2>
                <p class="text-xs text-slate-500">Ketinggian air per jam (01:00 - 24:00) pada tanggal {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-medium">
                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-sky-500"></span> Pasang (>0 cm)</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Surut (&lt;0 cm)</span>
            </div>
        </div>

        <div id="mainTidalChart" class="w-full h-80 sm:h-96"></div>
    </div>

    <!-- Monthly Trend & Data Table Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Monthly Overview Chart (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <div class="mb-4">
                <h3 class="text-base font-bold text-slate-900">Tren Pasang Maksimum & Surut Sebulan</h3>
                <p class="text-xs text-slate-500">Rentang HHW dan LLW harian selama bulan {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('F Y') }}</p>
            </div>
            <div id="monthlyTidalChart" class="w-full h-72"></div>
        </div>

        <!-- 24 Hours Detail Table (1 Col) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col">
            <div class="mb-3">
                <h3 class="text-base font-bold text-slate-900">Rincian Data Jam</h3>
                <p class="text-xs text-slate-500">Tabel 24 jam tanggal {{ $selectedDate }}</p>
            </div>

            <div class="flex-1 overflow-y-auto max-h-72 border border-slate-100 rounded-2xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 sticky top-0 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3">Jam</th>
                            <th class="py-2.5 px-3">Ketinggian</th>
                            <th class="py-2.5 px-3">Kondisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($hourlyData as $row)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2 px-3 font-mono font-bold text-slate-700">
                                    {{ sprintf('%02d:00', $row->hour) }}
                                </td>
                                <td class="py-2 px-3 font-bold {{ $row->water_level >= 0 ? 'text-sky-600' : 'text-emerald-600' }}">
                                    {{ $row->water_level > 0 ? '+' : '' }}{{ $row->water_level }} cm
                                </td>
                                <td class="py-2 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                        {{ $row->water_level >= 50 ? 'bg-amber-100 text-amber-800' : ($row->water_level >= 0 ? 'bg-sky-100 text-sky-800' : 'bg-emerald-100 text-emerald-800') }}">
                                        {{ $row->water_level >= 50 ? 'Pasang Tinggi' : ($row->water_level >= 0 ? 'Pasang' : 'Surut') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-slate-400">Data belum tersedia untuk tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hourlyRaw = @json($hourlyData);
        const monthlyRaw = @json($monthlyData);

        // 1. MAIN 24-HOUR TIDAL CURVE
        const hours = hourlyRaw.map(item => 'Jam ' + String(item.hour).padStart(2, '0') + ':00');
        const levels = hourlyRaw.map(item => item.water_level);

        const mainChartOptions = {
            series: [{
                name: 'Tinggi Air Laut (cm)',
                data: levels
            }],
            chart: {
                type: 'area',
                height: 360,
                toolbar: { show: true },
                zoom: { enabled: true }
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.6,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            colors: ['#0284c7'],
            grid: {
                borderColor: '#e2e8f0',
                strokeDashArray: 4
            },
            xaxis: {
                categories: hours,
                labels: {
                    rotate: -45,
                    style: { fontSize: '11px', colors: '#64748b' }
                }
            },
            yaxis: {
                title: {
                    text: 'Ketinggian dari MSL (cm)',
                    style: { fontSize: '12px', fontWeight: 600, color: '#64748b' }
                },
                labels: {
                    formatter: (val) => val + ' cm'
                }
            },
            annotations: {
                yaxis: [{
                    y: 0,
                    borderColor: '#94a3b8',
                    strokeDashArray: 2,
                    label: {
                        borderColor: '#94a3b8',
                        style: { color: '#fff', background: '#64748b', fontSize: '10px' },
                        text: 'MSL (0 cm)'
                    }
                }]
            },
            tooltip: {
                y: {
                    formatter: (val) => val + ' cm dari MSL'
                }
            }
        };

        new ApexCharts(document.getElementById('mainTidalChart'), mainChartOptions).render();

        // 2. MONTHLY OVERVIEW CHART
        const monthDates = monthlyRaw.map(item => item.record_date.substring(8, 10)); // Days
        const maxSeries = monthlyRaw.map(item => item.max_level);
        const minSeries = monthlyRaw.map(item => item.min_level);

        const monthlyOptions = {
            series: [
                { name: 'Pasang Maksimum (HHW)', data: maxSeries },
                { name: 'Surut Minimum (LLW)', data: minSeries }
            ],
            chart: {
                type: 'line',
                height: 280,
                toolbar: { show: false }
            },
            stroke: {
                curve: 'smooth',
                width: 2.5
            },
            colors: ['#0284c7', '#10b981'],
            xaxis: {
                categories: monthDates,
                title: { text: 'Tanggal Bulan Ini', style: { fontSize: '11px', color: '#64748b' } }
            },
            yaxis: {
                labels: { formatter: (val) => val + ' cm' }
            },
            grid: { borderColor: '#f1f5f9' },
            tooltip: {
                shared: true,
                y: { formatter: (val) => val + ' cm' }
            }
        };

        new ApexCharts(document.getElementById('monthlyTidalChart'), monthlyOptions).render();
    });
</script>
@endpush
