@extends('layouts.app')

@section('title', 'Kalender Pasang Surut - BMKG')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/kalender.css') }}">
@endpush

@section('content')
<div class="kalender-page">

    <div class="kalender-page__header">
        <h1>Kalender Pasang Surut</h1>
        <p>Lihat jadwal pasang surut air laut berdasarkan lokasi dan tanggal.</p>
    </div>

    {{-- ------- Cari Lokasi (dropdown, selalu di atas) ------- --}}
    <div class="kalender-search-card">
        <label class="kalender-search-card__label">Cari Lokasi</label>

        <div class="kalender-select" id="kalenderSelect">
            <button type="button" class="kalender-select__control" id="kalenderSelectControl">
                <span id="kalenderSelectValue" class="is-placeholder">Pilih Lokasi</span>
                <svg id="kalenderSelectChevron" width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M6 9l6 6 6-6" stroke="#334155" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <ul class="kalender-select__list" id="kalenderSelectList" hidden>
                @foreach ($locations as $loc)
                    <li class="kalender-select__item" data-slug="{{ $loc['slug'] }}" data-name="{{ $loc['name'] }}">
                        {{ $loc['name'] }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- ------- Grid: kalender + detail pasang/surut ------- --}}
    <div class="kalender-grid">

        <div class="kalender-card">
            <div class="kalender-card__header">
                <button type="button" class="kalender-nav" id="prevMonth" aria-label="Bulan sebelumnya">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M15 6l-6 6 6 6" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <button type="button" class="kalender-card__title" id="monthYearLabel">
                    <span id="monthYearText">Agustus 2026</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M9 6l6 6-6 6" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <button type="button" class="kalender-nav" id="nextMonth" aria-label="Bulan berikutnya">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M9 6l6 6-6 6" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>

            {{-- Panel ganti tahun/bulan langsung, muncul saat label bulan diklik --}}
            <div class="kalender-jump" id="kalenderJump" hidden>
                <button type="button" id="jumpYearPrev">&laquo;</button>
                <select id="jumpMonth"></select>
                <select id="jumpYear"></select>
                <button type="button" id="jumpYearNext">&raquo;</button>
            </div>

            <div class="kalender-weekdays">
                <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span>
                <span>Fri</span><span>Sat</span><span>Sun</span>
            </div>

            <div class="kalender-days" id="kalenderDays"></div>
        </div>

        <aside class="kalender-detail">

            <p class="kalender-detail__date" id="detailDateLabel" hidden></p>

            <div class="tide-card">
                <div class="tide-card__header tide-card__header--pasang">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M12 19V5M6 11l6-6 6 6" stroke="#1e3a8a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    PASANG
                </div>
                <div class="tide-card__body" id="pasangBody"></div>
            </div>

            <div class="tide-card">
                <div class="tide-card__header tide-card__header--surut">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M12 5v14M6 13l6 6 6-6" stroke="#1e3a8a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    SURUT
                </div>
                <div class="tide-card__body" id="surutBody"></div>
            </div>

        </aside>

    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/kalender.js') }}"></script>
@endpush