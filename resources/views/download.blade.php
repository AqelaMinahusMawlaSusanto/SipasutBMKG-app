@extends('layouts.app')

@section('title', 'Download Pasang Surut - BMKG Monitoring Pasang Surut')

@section('content')

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <section class="page-head">
        <h1 class="page-head__title">Kondisi Pasang Surut</h1>
        <p class="page-head__subtitle">Pantau kondisi pasang surut air laut secara real-time di lokasi terdekat</p>
    </section>

    {{-- ================= FILTER ================= --}}
    <section class="filter-card">
        <form method="GET" action="{{ route('download.index') }}" id="filterForm">
            <div class="filter-grid">
                <div class="filter-field">
                    <label for="lokasi">Cari Lokasi</label>
                    <select name="lokasi" id="lokasi">
                        <option value="">Pilih Lokasi</option>
                        @foreach ($locations as $loc)
                            <option value="{{ $loc['slug'] }}" @selected(($filters['lokasi'] ?? '') === $loc['slug'])>{{ $loc['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field filter-field--dates">
                    <label>Tanggal</label>
                    <div class="filter-field__row">
                        <input type="date" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}" placeholder="Tanggal Mulai">
                        <input type="date" name="tanggal_akhir" value="{{ $filters['tanggal_akhir'] ?? '' }}" placeholder="Tanggal Akhir">
                    </div>
                </div>

                <div class="filter-field">
                    <label for="urutkan">Urutkan</label>
                    <select name="urutkan" id="urutkan">
                        <option value="terkini" @selected(($filters['urutkan'] ?? 'terkini') === 'terkini')>Terkini</option>
                        <option value="terlama" @selected(($filters['urutkan'] ?? '') === 'terlama')>Terlama</option>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="status">Status Kondisi</label>
                    <select name="status" id="status">
                        <option value="semua">Semua Kondisi</option>
                        <option value="aman" @selected(($filters['status'] ?? '') === 'aman')>Aman</option>
                        <option value="waspada" @selected(($filters['status'] ?? '') === 'waspada')>Waspada</option>
                        <option value="bahaya" @selected(($filters['status'] ?? '') === 'bahaya')>Bahaya</option>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="zona">Zona Waktu</label>
                    <select name="zona" id="zona">
                        <option value="wib" selected>WIB (UTC+7)</option>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="datum">Datum</label>
                    <select name="datum" id="datum">
                        <option value="msl" selected>MSL</option>
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Cari
                </button>
            </div>
        </form>
    </section>

    {{-- ================= CHART + MAP ================= --}}
    <section class="chart-map-grid">
        <div class="chart-card">
            <div class="chart-card__head">
                <h2 class="chart-card__title">Grafik Pasang Surut</h2>
                <div class="chart-legend">
                    <span><i class="dot dot--blue"></i> Pasang</span>
                    <span><i class="dot dot--orange"></i> Surut</span>
                    <span><i class="dash"></i> Tinggi Saat Ini</span>
                </div>
                <div class="range-toggle">
                    <button type="button" class="range-toggle__btn is-active" data-range="24h">24 Jam</button>
                    <button type="button" class="range-toggle__btn" data-range="7d">7 Hari</button>
                </div>
            </div>
            <div class="chart-card__canvas-wrap">
                <canvas id="tideDetailChart" height="280"></canvas>
            </div>
            <p class="chart-card__footnote chart-card__footnote--muted">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 7v5l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Waktu dalam WIB (UTC+7)
            </p>
        </div>

        <div class="map-card">
            <h2 class="chart-card__title">Peta Lokasi Monitoring</h2>
            <div id="locationMap" class="map-card__canvas"></div>
        </div>
    </section>

    {{-- ================= TABEL ================= --}}
    <section class="table-card">
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Jam Pasang</th>
                        <th>Tinggi Pasang</th>
                        <th>Jam Surut</th>
                        <th>Tinggi Surut</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $r)
                        <tr>
                            <td>{{ $r['no'] }}</td>
                            <td>{{ $r['lokasi'] }}</td>
                            <td>{{ $r['tanggal'] }}</td>
                            <td>{{ $r['jam_pasang'] }}</td>
                            <td class="text-blue">{{ number_format($r['tinggi_pasang'], 2) }} m</td>
                            <td>{{ $r['jam_surut'] }}</td>
                            <td class="text-blue">{{ number_format($r['tinggi_surut'], 2) }} m</td>
                            <td><span class="badge badge--{{ $r['status'] }}">{{ ucfirst($r['status']) }}</span></td>
                            <td>{{ $r['update'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="table-empty">Tidak ada data untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <p class="table-footer__count">
                Menampilkan {{ count($rows) ? (($page - 1) * $perPage + 1) : 0 }} -
                {{ (($page - 1) * $perPage) + count($rows) }} dari {{ $totalRows }} data
            </p>

            <div class="pagination">
                @php $qs = request()->except('page'); @endphp
                <a class="pagination__btn {{ $page <= 1 ? 'is-disabled' : '' }}"
                   href="{{ $page > 1 ? route('download.index', array_merge($qs, ['page' => $page - 1])) : '#' }}">&lt;</a>

                @for ($i = 1; $i <= $totalPages; $i++)
                    <a class="pagination__btn {{ $i === $page ? 'is-active' : '' }}"
                       href="{{ route('download.index', array_merge($qs, ['page' => $i])) }}">{{ $i }}</a>
                @endfor

                <a class="pagination__btn {{ $page >= $totalPages ? 'is-disabled' : '' }}"
                   href="{{ $page < $totalPages ? route('download.index', array_merge($qs, ['page' => $page + 1])) : '#' }}">&gt;</a>
            </div>

            <a href="{{ route('download.export', request()->query()) }}" class="btn-primary btn-export">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 3v12m0 0 4-4m-4 4-4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Export Data
            </a>
        </div>
    </section>

    <script>
        window.MAP_LOCATIONS = @json($locations);
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/download.js') }}"></script>

@endsection