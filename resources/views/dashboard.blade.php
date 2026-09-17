@extends('layouts.app')

@section('title', 'Dashboard - BMKG Monitoring Pasang Surut')

@section('content')

    <section class="hero">
        <div class="hero__inner">
            <h1 class="hero__title">Monitoring Pasang Surut Air Laut</h1>
            <p class="hero__subtitle">
                Pantau kondisi pasang surut air laut secara langsung untuk mendukung
                aktivitas pelayaran dan perikanan.
            </p>

            {{-- Dropdown lokasi --}}
            <div class="location-select" id="locationSelect">
                <button type="button" class="location-select__trigger" id="locationTrigger">
                    <span class="location-select__pin" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 21s-7-6.1-7-11.5A7 7 0 0 1 19 9.5C19 14.9 12 21 12 21Z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="9.5" r="2.3" stroke="currentColor" stroke-width="2"/></svg>
                    </span>
                    <span id="locationLabel">{{ $active['label'] }}</span>
                    <svg class="location-select__chevron" width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="2"/></svg>
                </button>

                <ul class="location-select__menu" id="locationMenu">
                    @foreach ($locations as $slug => $loc)
                        @if ($slug !== $selected)
                            <li>
                                <a href="{{ route('dashboard.index', ['lokasi' => $slug]) }}">{{ $loc['label'] }}</a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <section class="stat-cards">
        <div class="stat-card">
            <span class="stat-card__icon stat-card__icon--tide" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M3 15c2 0 2-2 4-2s2 2 4 2 2-2 4-2 2 2 4 2 2-2 4-2" stroke="#fff" stroke-width="2" stroke-linecap="round"/><path d="M3 10c2 0 2-2 4-2s2 2 4 2 2-2 4-2 2 2 4 2 2-2 4-2" stroke="#fff" stroke-width="2" stroke-linecap="round" opacity=".6"/></svg>
            </span>
            <div>
                <p class="stat-card__label">Pasang Saat Ini</p>
                <p class="stat-card__value">{{ number_format($active['current_tide'], 1) }} m</p>
                <p class="stat-card__meta">{{ $active['current_time'] }}</p>
            </div>
        </div>

        <div class="stat-card">
            <span class="stat-card__icon stat-card__icon--status status--{{ $active['status'] }}" aria-hidden="true">
                @if ($active['status'] === 'aman')
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                @elseif ($active['status'] === 'waspada')
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 9v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                @else
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 9v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                @endif
            </span>
            <div>
                <p class="stat-card__label">Status</p>
                <p class="stat-card__value status-text--{{ $active['status'] }}">{{ $active['status_label'] }}</p>
                <p class="stat-card__meta">{{ $active['status_desc'] }}</p>
            </div>
        </div>

        <div class="stat-card">
            <span class="stat-card__icon stat-card__icon--wind" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M3 8h11a3 3 0 1 0-3-3M3 16h15a3 3 0 1 1-3 3M3 12h8" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>
            </span>
            <div>
                <p class="stat-card__label">Kecepatan Angin</p>
                <p class="stat-card__value">{{ $active['wind_speed'] }} km/jam</p>
                <p class="stat-card__meta">{{ $active['wind_dir'] }}</p>
            </div>
        </div>

        <div class="stat-card">
            <span class="stat-card__icon stat-card__icon--weather" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="4" stroke="#fff" stroke-width="2"/><path d="M12 3v2m0 14v2m9-9h-2M5 12H3m14.5-6.5-1.4 1.4M6.9 17.1l-1.4 1.4m0-13 1.4 1.4m11.2 11.2 1.4 1.4" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>
            </span>
            <div>
                <p class="stat-card__label">Cuaca</p>
                <p class="stat-card__value">{{ $active['weather'] }}</p>
                <p class="stat-card__meta">{{ $active['temperature'] }}&deg;C</p>
            </div>
        </div>
    </section>

    <section class="main-grid">
        <div class="chart-card">
            <h2 class="chart-card__title">Prediksi Pasang Surut Hari Ini</h2>
            <div class="chart-card__canvas-wrap">
                <canvas id="tideChart" height="260"></canvas>
            </div>
            <p class="chart-card__footnote">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 7v5l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                @if ($active['status'] === 'bahaya')
                    Potensi pasang tertinggi diperkirakan terjadi pada pukul {{ $active['next_high']['time'] }}
                @elseif ($active['status'] === 'waspada')
                    Prediksi pasang tinggi diperkirakan terjadi pada pukul {{ $active['next_high']['time'] }}
                @else
                    Prediksi pasang tertinggi berikutnya {{ number_format($active['next_high']['value'], 1) }} m pada pukul {{ $active['next_high']['time'] }}
                @endif
            </p>
        </div>

        <div class="side-col">
            <div class="info-card">
                <h2 class="info-card__title">Informasi Lokasi</h2>
                <ul class="info-list">
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 21s-7-6.1-7-11.5A7 7 0 0 1 19 9.5C19 14.9 12 21 12 21Z" stroke="currentColor" stroke-width="2"/></svg>
                        <span>
                            <strong>{{ $active['label'] }}</strong>
                            <small>{{ $active['kabupaten'] }}</small>
                        </span>
                    </li>
                    <li class="info-list__row">
                        <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/></svg> Update Terakhir</span>
                        <strong>{{ $active['updated_at'] }}</strong>
                    </li>
                    <li class="info-list__row">
                        <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M3 15c2 0 2-2 4-2s2 2 4 2 2-2 4-2 2 2 4 2 2-2 4-2" stroke="currentColor" stroke-width="2"/></svg> Ketinggian Terakhir</span>
                        <strong>{{ number_format($active['last_height'], 1) }} m</strong>
                    </li>
                    <li class="info-list__row">
                        <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 21s-7-6.1-7-11.5A7 7 0 0 1 19 9.5C19 14.9 12 21 12 21Z" stroke="currentColor" stroke-width="2"/></svg> Koordinat</span>
                        <strong>{{ $active['coordinate'] }}</strong>
                    </li>
                </ul>
            </div>

            <div class="warning-card warning-card--{{ $active['status'] }}">
                <span class="warning-card__icon" aria-hidden="true">
                    @if ($active['status'] === 'aman')
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @else
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 9v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                    @endif
                </span>
                <div>
                    <p class="warning-card__title">Peringatan Hari Ini</p>
                    <p class="warning-card__headline">{{ $active['warning_title'] }}</p>
                    <p class="warning-card__desc">{{ $active['warning_desc'] }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Data dikirim ke JS supaya dropdown & chart bisa langsung update tanpa reload halaman --}}
    <script>
        window.TIDE_DATA = @json($locations);
        window.SELECTED_LOCATION = @json($selected);
    </script>
    <script src="{{ asset('js/dashboard.js') }}"></script>

@endsection
