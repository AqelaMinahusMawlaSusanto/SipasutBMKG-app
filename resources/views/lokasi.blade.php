@extends('layouts.app')

@section('title', 'Lokasi Monitoring - BMKG')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('css/lokasi.css') }}">
@endpush

@section('content')
<div class="lokasi-page">

    <div class="lokasi-page__header">
        <h1>Lokasi Monitoring</h1>
        <p>Pantau kondisi pasang surut air laut secara real-time di berbagai lokasi</p>
    </div>

    <div class="lokasi-search-row">
        <div class="lokasi-search" id="lokasiSearch">
            <svg class="lokasi-search__icon" width="18" height="18" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="7" stroke="#94a3b8" stroke-width="2"/>
                <path d="M21 21l-4.3-4.3" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <input
                type="text"
                id="lokasiSearchInput"
                placeholder="Cari lokasi monitoring...."
                autocomplete="off"
            >
            <div class="lokasi-dropdown" id="lokasiDropdown" hidden>
                <p class="lokasi-dropdown__label">Hasil Pencarian</p>
                <ul id="lokasiDropdownList"></ul>
            </div>
        </div>

        <button type="button" class="btn-cari" id="btnCari">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="7" stroke="#fff" stroke-width="2"/>
                <path d="M21 21l-4.3-4.3" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Cari
        </button>
    </div>

    <div class="lokasi-grid">

        <div class="lokasi-map-card">
            <div id="lokasiMap"></div>

            <button type="button" class="legend-toggle" id="legendToggle" aria-expanded="false" aria-label="Tampilkan keterangan kondisi air">
                <svg id="legendChevron" width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M6 9l6 6 6-6" stroke="#334155" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <div class="legend-box" id="legendBox" hidden>
                <p class="legend-box__title">Kondisi Air</p>
                <ul>
                    <li>
                        <span class="dot dot--tinggi"></span>
                        <span><strong>Tinggi</strong><small>Tinggi air diatas normal</small></span>
                    </li>
                    <li>
                        <span class="dot dot--normal"></span>
                        <span><strong>Normal</strong><small>Tinggi air dalam kondisi normal</small></span>
                    </li>
                    <li>
                        <span class="dot dot--rendah"></span>
                        <span><strong>Rendah</strong><small>Tinggi air dibawah normal</small></span>
                    </li>
                </ul>
            </div>
        </div>

        <aside class="lokasi-info">

            <div class="info-banner">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;margin-top:1px;">
                    <circle cx="12" cy="12" r="9" stroke="#2563eb" stroke-width="2"/>
                    <path d="M12 11v5" stroke="#2563eb" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="12" cy="8" r="1" fill="#2563eb"/>
                </svg>
                <div>
                    <strong>Ketik nama lokasi pada kolom pencarian</strong>
                    <small>Contoh: Tanjung Perak, Surabaya Timur, Gresik</small>
                </div>
            </div>

            <div class="detail-card">
                <h2>Detail Lokasi</h2>

                <img id="detailImage" src="" alt="">

                <div class="detail-card__title-row">
                    <h3 id="detailName">-</h3>
                    <span class="badge" id="detailBadge">-</span>
                </div>

                <p class="detail-card__kabupaten" id="detailKabupaten">-</p>
                <p class="detail-card__koordinat">Koordinat: <span id="detailKoordinat">-</span></p>

                <h4>Informasi Lainnya</h4>

                <div class="detail-card__row">
                    <span>ID Lokasi</span>
                    <strong id="detailIdLokasi">-</strong>
                </div>
                <div class="detail-card__row">
                    <span>Jenis Lokasi</span>
                    <strong id="detailJenisLokasi">-</strong>
                </div>
                <div class="detail-card__row">
                    <span>Sensor Terakhir Update</span>
                    <strong id="detailSensor">-</strong>
                </div>
            </div>

        </aside>

    </div>
</div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Data lokasi dikirim dari LokasiController -> dipakai oleh public/js/lokasi.js
        window.LOKASI_DATA = @json(array_values($locations));
        window.LOKASI_SELECTED = @json($selected);
    </script>
    <script src="{{ asset('js/lokasi.js') }}"></script>
@endpush
