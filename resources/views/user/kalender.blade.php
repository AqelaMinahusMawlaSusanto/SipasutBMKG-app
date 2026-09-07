@extends('layouts.app')

@section('title', 'Kalender Pasang Surut')

@section('content')
<div class="space-y-6">

    <!-- Header & Filter Section -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 text-xs font-semibold text-sky-600 mb-1">
                <span>Prediksi & Catatan Harian Pasang Surut</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Kalender Pasang Surut</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pilih lokasi stasiun untuk melihat ringkasan status air per hari. Klik tanggal untuk rincian kurva 24 jam.</p>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('user.calendar') }}" class="flex flex-wrap items-center gap-3">
            <div>
                <label for="location_id" class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Pilih Lokasi</label>
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
                <label for="month" class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Bulan</label>
                <select name="month" id="month" onchange="this.form.submit()"
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold bg-slate-50 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <label for="year" class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Tahun</label>
                <select name="year" id="year" onchange="this.form.submit()"
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold bg-slate-50 focus:ring-2 focus:ring-sky-500 focus:outline-none">
                    @for($y = 2024; $y <= 2027; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </form>
    </div>

    <!-- Calendar Month Navigation & Legend -->
    <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2">
            <span class="text-sm font-bold text-slate-800">
                {{ \Carbon\Carbon::create($year, $month, 1)->translatedFormat('F Y') }}
            </span>
            <span class="text-[11px] text-sky-600 font-bold px-2 py-0.5 rounded bg-sky-50">
                {{ $selectedLocation->name }}
            </span>
        </div>

        <!-- Legend Badges -->
        <div class="flex items-center gap-3 text-[11px] font-semibold text-slate-600">
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Pasang Tinggi (&ge; +100 cm)</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span> Pasang Normal</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Surut</span>
        </div>
    </div>

    <!-- Calendar Grid (31 Days) -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
        <!-- Day Names Header -->
        <div class="grid grid-cols-7 gap-2 text-center text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
            <div>Min</div>
            <div>Sen</div>
            <div>Sel</div>
            <div>Rab</div>
            <div>Kam</div>
            <div>Jum</div>
            <div>Sab</div>
        </div>

        @php
            $firstDayOfMonth = \Carbon\Carbon::create($year, $month, 1);
            $daysInMonth = $firstDayOfMonth->daysInMonth;
            $startDayOfWeek = $firstDayOfMonth->dayOfWeek; // 0 (Sun) to 6 (Sat)
        @endphp

        <!-- Days Grid -->
        <div class="grid grid-cols-7 gap-2">
            <!-- Empty offset cells -->
            @for($blank = 0; $blank < $startDayOfWeek; $blank++)
                <div class="min-h-24 sm:min-h-28 rounded-2xl bg-slate-50/50 border border-transparent"></div>
            @endfor

            <!-- Day Cells -->
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $dayData = $calendarDays->get($day);
                    $max = $dayData?->max_level ?? 0;
                    $min = $dayData?->min_level ?? 0;
                    $dateString = sprintf('%04d-%02d-%02d', $year, $month, $day);

                    // Badge color logic
                    $borderClass = $max >= 100 ? 'hover:border-amber-400 bg-amber-50/20' : ($max >= 0 ? 'hover:border-sky-400 bg-white' : 'hover:border-emerald-400 bg-emerald-50/10');
                @endphp

                <div class="min-h-24 sm:min-h-28 rounded-2xl p-2.5 border border-slate-200/80 {{ $borderClass }} hover:shadow-md transition cursor-pointer flex flex-col justify-between group"
                     onclick="openDayModal('{{ $dateString }}', '{{ $day }}', {{ $selectedLocation->id }}, '{{ $selectedLocation->name }}')">
                    
                    <div class="flex items-center justify-between">
                        <span class="text-xs sm:text-sm font-extrabold text-slate-800 group-hover:text-sky-600 transition">
                            {{ $day }}
                        </span>
                        @if($max >= 100)
                            <span class="w-2 h-2 rounded-full bg-amber-500" title="Pasang Tinggi"></span>
                        @elseif($max >= 0)
                            <span class="w-2 h-2 rounded-full bg-sky-500" title="Pasang"></span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-emerald-500" title="Surut"></span>
                        @endif
                    </div>

                    @if($dayData)
                        <div class="mt-1 space-y-0.5 text-[10px] font-semibold">
                            <div class="text-sky-700 truncate">H: +{{ $max }} cm</div>
                            <div class="text-emerald-700 truncate">L: {{ $min }} cm</div>
                        </div>
                    @else
                        <div class="text-[10px] text-slate-400 italic">No data</div>
                    @endif

                    <div class="text-[9px] text-slate-400 text-right opacity-0 group-hover:opacity-100 transition">
                        Klik &rarr;
                    </div>
                </div>
            @endfor
        </div>
    </div>

</div>

<!-- Modal Pop-Up: Rincian Pasang Surut 24 Jam Hari Terpilih -->
<div id="dayDetailModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative">
        <button type="button" onclick="closeDayModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div>
            <span class="text-xs font-bold text-sky-600 uppercase" id="modalLocationName">Stasiun BMKG</span>
            <h3 class="text-lg font-extrabold text-slate-900 mt-0.5" id="modalDateTitle">Detail Pasang Surut</h3>
            <p class="text-xs text-slate-500 mt-0.5">Grafik fluktuasi per jam 01:00 sampai 24:00</p>
        </div>

        <div class="mt-4">
            <div id="modalHourlyChart" class="w-full h-64"></div>
        </div>

        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
            <a href="#" id="modalDirectLink" class="text-xs font-bold text-sky-600 hover:underline">
                Buka di Halaman Kondisi Pasang Surut &rarr;
            </a>
            <button type="button" onclick="closeDayModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let modalChart = null;

    function openDayModal(dateStr, dayNum, locId, locName) {
        document.getElementById('modalLocationName').innerText = locName;
        document.getElementById('modalDateTitle').innerText = 'Tanggal ' + dateStr;
        document.getElementById('modalDirectLink').href = '/kondisi-pasang-surut?location_id=' + locId + '&date=' + dateStr;
        document.getElementById('dayDetailModal').classList.remove('hidden');

        // Fetch hourly detail via AJAX API
        fetch('/api/day-details?location_id=' + locId + '&date=' + dateStr)
            .then(res => res.json())
            .then(res => {
                const hours = res.hours.map(h => 'Jam ' + String(h).padStart(2, '0') + ':00');
                const levels = res.levels;

                const options = {
                    series: [{ name: 'Ketinggian Air (cm)', data: levels }],
                    chart: { type: 'area', height: 260, toolbar: { show: false } },
                    stroke: { curve: 'smooth', width: 2.5 },
                    colors: ['#0284c7'],
                    fill: {
                        type: 'gradient',
                        gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.05, stops: [0, 90, 100] }
                    },
                    xaxis: { categories: hours, labels: { rotate: -45, style: { fontSize: '10px' } } },
                    yaxis: { labels: { formatter: val => val + ' cm' } },
                    tooltip: { y: { formatter: val => val + ' cm MSL' } }
                };

                if (modalChart) {
                    modalChart.destroy();
                }
                modalChart = new ApexCharts(document.getElementById('modalHourlyChart'), options);
                modalChart.render();
            });
    }

    function closeDayModal() {
        document.getElementById('dayDetailModal').classList.add('hidden');
    }
</script>
@endpush
