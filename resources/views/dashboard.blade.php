@extends('layouts.app')

@section('title', 'Dashboard - BMKG Monitoring Pasang Surut')

@section('content')

    {{-- Banner Notifikasi Peringatan Dinamis dari Admin --}}
    <div id="liveNotifBanner" class="alert-banner alert-banner--{{ $notification ? $notification->type : 'info' }}" style="{{ $notification ? '' : 'display: none;' }}; margin: 16px 48px -8px; border-radius: 10px; padding: 14px 20px; display: {{ $notification ? 'flex' : 'none' }}; align-items: center; justify-content: space-between; gap: 14px; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
        <div style="display: flex; align-items: center; gap: 12px;">
            <span style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; background: rgba(0,0,0,0.08); flex-shrink: 0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 9v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
            </span>
            <div>
                <strong id="liveNotifTitle" style="font-size: 14px; display: block;">{{ $notification ? $notification->title : '' }}</strong>
                <span id="liveNotifMsg" style="font-size: 13px; opacity: 0.9;">{{ $notification ? $notification->message : '' }}</span>
            </div>
        </div>
        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 4px 8px; border-radius: 6px; background: rgba(255,255,255,0.3);">
            Pemberitahuan Admin
        </span>
    </div>

    <section class="hero">
        <div class="hero__inner">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <h1 class="hero__title" style="margin: 0;">Monitoring Pasang Surut Air Laut</h1>
            </div>
            
            <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.18); backdrop-filter: blur(4px); padding: 5px 12px; border-radius: 20px; margin-bottom: 14px; font-size: 12px; color: #fff;">
                <span id="livePulseDot" style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e; display: inline-block; box-shadow: 0 0 8px #22c55e;"></span>
                <span id="liveStatusText">Live Data Terhubung</span>
                <span style="opacity: 0.6;">•</span>
                <span id="liveTimeBadge">{{ now()->format('H:i') }} WIB</span>
            </div>

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
                <p class="stat-card__value" id="valCurrentTide">{{ number_format($active['current_tide'], 1) }} m</p>
                <p class="stat-card__meta" id="valCurrentTime">{{ $active['current_time'] }}</p>
            </div>
        </div>

        <div class="stat-card">
            <span class="stat-card__icon stat-card__icon--status status--{{ $active['status'] }}" id="iconStatus" aria-hidden="true">
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
                <p class="stat-card__value status-text--{{ $active['status'] }}" id="valStatusLabel">{{ $active['status_label'] }}</p>
                <p class="stat-card__meta" id="valStatusDesc">{{ $active['status_desc'] }}</p>
            </div>
        </div>

        <div class="stat-card">
            <span class="stat-card__icon stat-card__icon--wind" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M3 8h11a3 3 0 1 0-3-3M3 16h15a3 3 0 1 1-3 3M3 12h8" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>
            </span>
            <div>
                <p class="stat-card__label">Kecepatan Angin</p>
                <p class="stat-card__value" id="valWindSpeed">{{ $active['wind_speed'] }} km/jam</p>
                <p class="stat-card__meta" id="valWindDir">{{ $active['wind_dir'] }}</p>
            </div>
        </div>

        <div class="stat-card">
            <span class="stat-card__icon stat-card__icon--weather" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="4" stroke="#fff" stroke-width="2"/><path d="M12 3v2m0 14v2m9-9h-2M5 12H3m14.5-6.5-1.4 1.4M6.9 17.1l-1.4 1.4m0-13 1.4 1.4m11.2 11.2 1.4 1.4" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg>
            </span>
            <div>
                <p class="stat-card__label">Cuaca</p>
                <p class="stat-card__value" id="valWeather">{{ $active['weather'] }}</p>
                <p class="stat-card__meta" id="valTemperature">{{ $active['temperature'] }}&deg;C</p>
            </div>
        </div>
    </section>

    <section class="main-grid">
        <div class="chart-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <h2 class="chart-card__title" style="margin: 0;">Prediksi Pasang Surut Hari Ini</h2>
                <span id="chartLiveIndicator" style="font-size: 11px; font-weight: 600; color: #64748b; display: inline-flex; align-items: center; gap: 5px;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #0284c7;"></span> Auto Sync
                </span>
            </div>
            <div class="chart-card__canvas-wrap">
                <canvas id="tideChart" height="260"></canvas>
            </div>
            <p class="chart-card__footnote" id="chartFootnote">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 7v5l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                <span id="chartFootnoteText">
                    @if ($active['status'] === 'bahaya')
                        Potensi pasang tertinggi diperkirakan terjadi pada pukul {{ $active['next_high']['time'] }}
                    @elseif ($active['status'] === 'waspada')
                        Prediksi pasang tinggi diperkirakan terjadi pada pukul {{ $active['next_high']['time'] }}
                    @else
                        Prediksi pasang tertinggi berikutnya {{ number_format($active['next_high']['value'], 1) }} m pada pukul {{ $active['next_high']['time'] }}
                    @endif
                </span>
            </p>
        </div>

        <div class="side-col">
            <div class="info-card">
                <h2 class="info-card__title">Informasi Lokasi</h2>
                <ul class="info-list">
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 21s-7-6.1-7-11.5A7 7 0 0 1 19 9.5C19 14.9 12 21 12 21Z" stroke="currentColor" stroke-width="2"/></svg>
                        <span>
                            <strong id="valInfoLabel">{{ $active['label'] }}</strong>
                            <small id="valInfoKab">{{ $active['kabupaten'] }}</small>
                        </span>
                    </li>
                    <li class="info-list__row">
                        <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/></svg> Update Terakhir</span>
                        <strong id="valUpdatedAt">{{ $active['updated_at'] }}</strong>
                    </li>
                    <li class="info-list__row">
                        <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M3 15c2 0 2-2 4-2s2 2 4 2 2-2 4-2 2 2 4 2 2-2 4-2" stroke="currentColor" stroke-width="2"/></svg> Ketinggian Terakhir</span>
                        <strong id="valLastHeight">{{ number_format($active['last_height'], 1) }} m</strong>
                    </li>
                    <li class="info-list__row">
                        <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 21s-7-6.1-7-11.5A7 7 0 0 1 19 9.5C19 14.9 12 21 12 21Z" stroke="currentColor" stroke-width="2"/></svg> Koordinat</span>
                        <strong id="valCoordinate">{{ $active['coordinate'] }}</strong>
                    </li>
                </ul>
            </div>

            <div class="warning-card warning-card--{{ $active['status'] }}" id="warningCard">
                <span class="warning-card__icon" id="warningCardIcon" aria-hidden="true">
                    @if ($active['status'] === 'aman')
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @else
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 9v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>
                    @endif
                </span>
                <div>
                    <p class="warning-card__title">Peringatan Hari Ini</p>
                    <p class="warning-card__headline" id="valWarningTitle">{{ $active['warning_title'] }}</p>
                    <p class="warning-card__desc" id="valWarningDesc">{{ $active['warning_desc'] }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Data dikirim ke JS supaya dropdown & chart bisa langsung update tanpa reload halaman --}}
    <script>
        window.TIDE_DATA = @json($locations);
        window.SELECTED_LOCATION = @json($selected);
        window.REALTIME_URL = "{{ route('dashboard.realtime') }}";
    </script>
    <script src="{{ asset('js/dashboard.js') }}"></script>

@endsection
