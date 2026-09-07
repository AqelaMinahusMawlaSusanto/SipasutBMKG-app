@extends('layouts.app')

@section('title', 'Lokasi Monitoring')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 text-xs font-semibold text-sky-600 mb-1">
                <span>Peta Geografis Pengamatan BMKG</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Peta Lokasi Monitoring Pasang Surut</h1>
            <p class="text-xs text-slate-500 mt-0.5">Sebaran 5 titik stasiun maritim pantau muka air laut di pesisir Jawa Timur</p>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-xl">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> 5 Titik Aktif
            </span>
        </div>
    </div>

    <!-- Map & Sidebar Split Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Interactive Leaflet Map Container (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-4 border border-slate-200/80 shadow-xs flex flex-col">
            <div class="flex items-center justify-between px-3 py-2 text-xs font-medium text-slate-500 border-b border-slate-100 mb-3">
                <span>Klik pada pin/marker untuk melihat kondisi stasiun</span>
                <span class="text-[11px] bg-sky-50 text-sky-700 px-2 py-0.5 rounded font-semibold">Jawa Timur & Selat Madura</span>
            </div>
            
            <div id="monitoringMap" class="w-full h-[480px] sm:h-[540px] rounded-2xl overflow-hidden z-10"></div>
        </div>

        <!-- Location List & Details Sidebar (1 Col) -->
        <div class="space-y-3 flex flex-col">
            <h3 class="text-sm font-bold text-slate-800 px-1">Daftar Titik Pengamatan (5 Stasiun)</h3>

            <div class="space-y-3 overflow-y-auto max-h-[540px] pr-1">
                @foreach($geoLocations as $loc)
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs hover:border-sky-400 hover:shadow-md transition cursor-pointer group"
                         onclick="focusMapMarker({{ $loc['lat'] }}, {{ $loc['lng'] }}, '{{ $loc['name'] }}')">
                        
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-100 text-slate-600">
                                    {{ $loc['code'] }}
                                </span>
                                <h4 class="text-sm font-bold text-slate-900 mt-1 group-hover:text-sky-600 transition">
                                    {{ $loc['name'] }}
                                </h4>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                {{ $loc['level'] >= 50 ? 'bg-amber-100 text-amber-800' : ($loc['level'] >= 0 ? 'bg-sky-100 text-sky-800' : 'bg-emerald-100 text-emerald-800') }}">
                                {{ $loc['status'] }}
                            </span>
                        </div>

                        <p class="text-xs text-slate-500 mt-2 line-clamp-2">{{ $loc['description'] }}</p>

                        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <div class="flex items-baseline gap-1">
                                <span class="text-base font-extrabold {{ $loc['level'] >= 0 ? 'text-sky-600' : 'text-emerald-600' }}">
                                    {{ $loc['level'] > 0 ? '+' : '' }}{{ $loc['level'] }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-semibold">cm MSL</span>
                            </div>

                            <a href="{{ route('user.kondisi', ['location_id' => $loc['id']]) }}" 
                               class="text-xs font-bold text-sky-600 hover:underline flex items-center gap-1"
                               onclick="event.stopPropagation()">
                                <span>Lihat Grafik</span> &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    let map;
    const markers = {};

    document.addEventListener('DOMContentLoaded', function() {
        const locations = @json($geoLocations);

        // Center map to East Java (Surabaya / Madura Strait)
        map = L.map('monitoringMap').setView([-7.5, 113.3], 8);

        // OpenStreetMap Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors | BMKG Maritim Perak'
        }).addTo(map);

        // Custom Marker Icon SVG
        const customIcon = L.divIcon({
            className: 'custom-pin',
            html: `<div style="background:#0284c7; width:28px; height:28px; border-radius:50%; border:3px solid #ffffff; box-shadow:0 4px 10px rgba(0,0,0,0.3); display:flex; align-items:center; justify-content:center; color:white;">
                    <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                   </div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14],
            popupAnchor: [0, -16]
        });

        locations.forEach(function(loc) {
            const popupContent = `
                <div style="font-family:sans-serif; min-width:180px;">
                    <div style="font-size:10px; font-weight:bold; color:#0284c7; text-transform:uppercase;">${loc.code}</div>
                    <div style="font-size:14px; font-weight:bold; color:#0f172a; margin-top:2px;">${loc.name}</div>
                    <div style="font-size:11px; color:#64748b; margin-top:2px;">${loc.institution}</div>
                    <div style="margin-top:8px; padding-top:6px; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <span style="font-size:10px; color:#94a3b8; display:block;">Level Air:</span>
                            <strong style="font-size:15px; color:${loc.level >= 0 ? '#0284c7' : '#10b981'};">${loc.level > 0 ? '+' : ''}${loc.level} cm</strong>
                        </div>
                        <span style="font-size:10px; font-weight:bold; background:#e0f2fe; color:#0369a1; padding:2px 6px; border-radius:4px;">${loc.status}</span>
                    </div>
                    <div style="margin-top:10px;">
                        <a href="/kondisi-pasang-surut?location_id=${loc.id}" style="display:block; text-align:center; background:#0284c7; color:#fff; padding:5px 10px; border-radius:8px; font-size:11px; font-weight:bold; text-decoration:none;">Buka Grafik Pasang Surut &rarr;</a>
                    </div>
                </div>
            `;

            const m = L.marker([loc.lat, loc.lng], { icon: customIcon })
                .addTo(map)
                .bindPopup(popupContent);

            markers[loc.name] = m;
        });
    });

    function focusMapMarker(lat, lng, name) {
        if (!map) return;
        map.flyTo([lat, lng], 11, { duration: 1.2 });
        if (markers[name]) {
            setTimeout(() => {
                markers[name].openPopup();
            }, 1200);
        }
    }
</script>
@endpush
