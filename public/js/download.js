/**
 * Halaman Download Pasang Surut
 *  - Grafik mengikuti data dari server (window.TIDE_CHART, disiapkan DownloadController)
 *  - Peta lokasi (Leaflet) tampilannya SAMA dengan peta di halaman Lokasi (public/js/lokasi.js)
 *    dan memakai window.MAP_LOCATIONS
 */

const BLUE = '#2563eb';    // Pasang
const ORANGE = '#f97316';  // Surut
const SLATE = '#64748b';   // Tinggi saat ini

// 5.25 -> "05:15"
function hhmm(h) {
    const m = Math.round(h * 60);
    return String(Math.floor(m / 60)).padStart(2, '0') + ':' + String(m % 60).padStart(2, '0');
}

// Tinggi air pada jam tertentu (interpolasi lurus antar titik)
function valueAt(points, h) {
    if (!points.length) return null;
    if (h <= points[0].h) return points[0].v;
    for (let i = 1; i < points.length; i++) {
        if (h <= points[i].h) {
            const a = points[i - 1];
            const b = points[i];
            return a.v + (b.v - a.v) * (h - a.h) / ((b.h - a.h) || 1);
        }
    }
    return points[points.length - 1].v;
}

// Batas sumbu Y mengikuti data, dibulatkan ke 0,5 dengan sedikit ruang untuk label
function yRange(values) {
    const lo = Math.min(...values);
    const hi = Math.max(...values);
    return { min: Math.floor((lo - 0.3) * 2) / 2, max: Math.ceil((hi + 0.3) * 2) / 2 };
}

// Menggambar label nilai, garis putus-putus, dan badge jam di bawah sumbu X
function makeMarkerPlugin(markers) {
    return {
        id: 'tideMarkers',
        afterDatasetsDraw(chart) {
            const { ctx, scales } = chart;
            const px = (h) => scales.x.getPixelForValue(h);
            const py = (v) => scales.y.getPixelForValue(v);

            markers.forEach((m) => {
                const x = px(m.h);
                const y = py(m.v);

                ctx.save();

                // garis putus-putus dari titik ke sumbu X
                ctx.setLineDash([3, 3]);
                ctx.strokeStyle = '#cbd5e1';
                ctx.beginPath();
                ctx.moveTo(x, y);
                ctx.lineTo(x, scales.y.bottom);
                ctx.stroke();
                ctx.setLineDash([]);

                // label nilai: pasang di atas titik, surut di bawah titik, saat ini di atas
                ctx.font = '700 12px Segoe UI, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillStyle = m.color;
                const prefix = { high: 'Pasang ', low: 'Surut ', now: 'Saat ini ' }[m.kind] || '';
                const text = prefix + m.v.toFixed(2) + ' m';
                ctx.fillText(text, x, m.kind === 'low' ? y + 20 : y - 12);

                // titik untuk penanda "saat ini"
                if (m.kind === 'now') {
                    ctx.fillStyle = m.color;
                    ctx.beginPath();
                    ctx.arc(x, y, 4, 0, Math.PI * 2);
                    ctx.fill();
                }

                // badge jam di bawah label sumbu X
                ctx.font = '700 11px Segoe UI, sans-serif';
                const w = ctx.measureText(m.time).width + 16;
                const boxY = scales.x.bottom + 4;
                ctx.fillStyle = m.color;
                ctx.fillRect(x - w / 2, boxY, w, 20);
                ctx.fillStyle = '#fff';
                ctx.fillText(m.time, x, boxY + 14);

                ctx.restore();
            });
        },
    };
}

document.addEventListener('DOMContentLoaded', () => {
    const data = window.TIDE_CHART || null;
    const canvas = document.getElementById('tideDetailChart');
    const info = document.getElementById('chartInfo');
    let chart = null;

    const setInfo = (text) => { if (info) info.textContent = text; };

    // ---------- Grafik 24 jam ----------
    function draw24h() {
        const pts = data.points;
        const high = data.high;
        const low = data.low;
        const range = yRange(pts.map((p) => p.v));

        const markers = [];
        if (high) markers.push({ h: high.h, v: high.v, time: high.time, color: BLUE, kind: 'high' });
        if (low) markers.push({ h: low.h, v: low.v, time: low.time, color: BLUE, kind: 'low' });
        if (data.isToday && data.nowH !== null) {
            markers.push({ h: data.nowH, v: valueAt(pts, data.nowH), time: data.nowTime, color: SLATE, kind: 'now' });
        }

        const isExtreme = (p) =>
            (high && Math.abs(p.h - high.h) < 0.01 && Math.abs(p.v - high.v) < 0.011) ||
            (low && Math.abs(p.h - low.h) < 0.01 && Math.abs(p.v - low.v) < 0.011);

        return new Chart(canvas, {
            type: 'line',
            data: {
                datasets: [{
                    data: pts.map((p) => ({ x: p.h, y: p.v })),
                    borderWidth: 2.5,
                    tension: 0.35,
                    fill: true,
                    backgroundColor: 'rgba(37, 99, 235, 0.08)',
                    pointRadius: (c) => (isExtreme(pts[c.dataIndex]) ? 5 : 0),
                    borderColor: BLUE,
                    pointBackgroundColor: BLUE,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }],
            },
            plugins: [makeMarkerPlugin(markers)],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 8, bottom: 30 } },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: (items) => hhmm(items[0].parsed.x) + ' WIB',
                            label: (item) => item.parsed.y.toFixed(2) + ' m',
                        },
                    },
                },
                scales: {
                    x: {
                        type: 'linear',
                        min: 0,
                        max: 24,
                        grid: { display: false },
                        ticks: { stepSize: 3, callback: (v) => hhmm(v) },
                    },
                    y: {
                        min: range.min,
                        max: range.max,
                        title: { display: true, text: 'Tinggi (m)', color: '#94a3b8', font: { size: 11 } },
                        ticks: { stepSize: 0.5 },
                        grid: { color: '#f1f5f9' },
                    },
                },
            },
        });
    }

    // ---------- Grafik 7 hari ----------
    function draw7d() {
        const week = data.week;
        const range = yRange(week.flatMap((w) => [w.hi, w.lo]));

        return new Chart(canvas, {
            type: 'line',
            data: {
                labels: week.map((w) => w.d),
                datasets: [
                    { label: 'Pasang', data: week.map((w) => w.hi), borderColor: BLUE, backgroundColor: BLUE, borderWidth: 2.5, tension: 0.3, pointRadius: 4 },
                    { label: 'Surut', data: week.map((w) => w.lo), borderColor: ORANGE, backgroundColor: ORANGE, borderWidth: 2.5, tension: 0.3, pointRadius: 4 },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 8, bottom: 8 } },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (item) => item.dataset.label + ': ' + item.parsed.y.toFixed(2) + ' m' } },
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        min: range.min,
                        max: range.max,
                        title: { display: true, text: 'Tinggi (m)', color: '#94a3b8', font: { size: 11 } },
                        ticks: { stepSize: 0.5 },
                        grid: { color: '#f1f5f9' },
                    },
                },
            },
        });
    }

    // Legenda: item 1-2 (Pasang, Surut) hanya dipakai grafik 7 Hari yang punya dua garis
    const legendItems = document.querySelectorAll('.chart-legend span');
    function setLegend(mode) {
        legendItems.forEach((el, i) => {
            if (i < 2) el.style.display = mode === '7d' ? '' : 'none';
        });
    }

    function render(mode) {
        if (!canvas || !data) return;
        setLegend(mode);
        if (chart) chart.destroy();

        if (mode === '7d') {
            if (data.week.length < 1) {
                setInfo('Data 7 hari belum tersedia untuk ' + data.location + '.');
                chart = null;
                return;
            }
            chart = draw7d();
            setInfo(data.location + ' · ' + data.weekStart + ' - ' + data.date + ' · Pasang dan surut harian (' + data.week.length + ' hari ada data)');
            return;
        }

        if (!data.points.length) {
            setInfo('Data jam pasang dan surut untuk ' + data.location + ' (' + data.date + ') belum lengkap, grafik 24 jam belum bisa ditampilkan.');
            chart = null;
            return;
        }
        chart = draw24h();
        setInfo(
            data.location + ' · ' + data.date + ' · ' +
            (data.source === 'hourly'
                ? 'Data tinggi air per jam'
                : 'Kurva diperkirakan dari titik pasang dan surut harian (data per jam belum diunggah)')
        );
    }

    // ---------- Tombol 24 Jam / 7 Hari ----------
    document.querySelectorAll('.range-toggle__btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.range-toggle__btn').forEach((b) => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            render(btn.dataset.range === '7d' ? '7d' : '24h');
        });
    });

    if (!data) {
        setInfo('Belum ada data untuk ditampilkan di grafik. Data muncul setelah admin mengunggahnya lewat Kelola Data.');
    } else {
        render('24h');
    }

    // ---------- Peta lokasi monitoring (Leaflet + OpenStreetMap) ----------
    // Gambar peta disamakan dengan halaman Lokasi. Klik titik -> pindah ke halaman Lokasi.
    // Data titik di bawah disalin dari LokasiController (kalau data di sana berubah, ubah juga di sini).
    const LOKASI_DATA = {
        kal:    { slug: 'kalianget', status: 'normal', lat: -7.0426, lng: 113.9276,
                  areas: [{ name: 'Pamekasan', lat: -7.1568, lng: 113.4824 }] },
        sbyplb: { slug: 'surabaya-pelabuhan', status: 'normal', lat: -7.2019, lng: 112.7379,
                  areas: [{ name: 'Surabaya Utara', lat: -7.2150, lng: 112.7400 },
                          { name: 'Benowo', lat: -7.2370, lng: 112.6200 }] },
        sbytim: { slug: 'surabaya-timur', status: 'normal', lat: -7.2854, lng: 112.7947,
                  areas: [{ name: 'Pasuruan', lat: -7.6453, lng: 112.9075 },
                          { name: 'Sidoarjo', lat: -7.4478, lng: 112.7183 },
                          { name: 'Probolinggo', lat: -7.7543, lng: 113.2159 },
                          { name: 'Jember', lat: -8.1845, lng: 113.6681 },
                          { name: 'Kenjeran', lat: -7.2400, lng: 112.7900 },
                          { name: 'Bangkalan Selatan', lat: -7.1000, lng: 112.7500 },
                          { name: 'Kuanyar', lat: -7.1280, lng: 112.9150 },
                          { name: 'Sukolilo', lat: -7.2950, lng: 112.7950 },
                          { name: 'Sampang', lat: -7.1887, lng: 113.2397 },
                          { name: 'Sepanjang Selat Madura', lat: -7.2000, lng: 112.8500 },
                          { name: 'Kreseh (barat Sampang)', lat: -7.2100, lng: 113.0800 }] },
        sbybrt: { slug: 'surabaya-barat', status: 'tinggi', lat: -7.2429, lng: 112.6913,
                  areas: [{ name: 'Gresik', lat: -7.1539, lng: 112.6561 },
                          { name: 'Lamongan', lat: -6.8730, lng: 112.2060 },
                          { name: 'Tuban', lat: -6.8976, lng: 112.0647 }] },
        bwi:    { slug: 'banyuwangi', status: 'normal', lat: -8.2192, lng: 114.3691,
                  areas: [{ name: 'Banyuwangi', lat: -8.2192, lng: 114.3691 }] },
    };
    const STATUS_COLOR = { tinggi: '#dc2626', normal: '#16a34a', rendah: '#2563eb' };

    const mapEl = document.getElementById('locationMap');
    const mapLocs = window.MAP_LOCATIONS || [];

    if (mapEl && window.L && mapLocs.length) {
        // Lokasi yang sedang tampil di grafik -> pin besar
        const chartLoc = data ? mapLocs.find((l) => l.label === data.location) : null;
        const selectedCode = chartLoc ? chartLoc.slug : null;

        // Ikon pin peta (SVG + style langsung, tidak butuh CSS tambahan).
        // Lokasi yang sedang tampil di grafik dibuat sedikit lebih besar.
        const pinIcon = (color, big) => {
            const w = big ? 34 : 24;
            const h = big ? 46 : 33;
            return L.divIcon({
                html: '<svg width="' + w + '" height="' + h + '" viewBox="0 0 34 46" xmlns="http://www.w3.org/2000/svg" ' +
                    'style="display:block;filter:drop-shadow(0 2px 3px rgba(15,23,42,.4))">' +
                    '<path d="M17 0C7.6 0 0 7.6 0 17c0 12.7 17 29 17 29s17-16.3 17-29C34 7.6 26.4 0 17 0z" fill="' + color + '"/>' +
                    '<circle cx="17" cy="17" r="7" fill="#fff"/></svg>',
                className: '',
                iconSize: [w, h],
                iconAnchor: [w / 2, h],
            });
        };

        const map = L.map('locationMap', { zoomControl: true, scrollWheelZoom: false }).setView([-7.4, 113.2], 8);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const allMarkers = [];
        mapLocs.forEach((loc) => {
            const info = LOKASI_DATA[loc.slug];
            if (!info && (loc.lat === null || loc.lng === null)) return;

            const slug = info ? info.slug : loc.slug;
            const color = STATUS_COLOR[info ? info.status : 'normal'];
            const isSelected = loc.slug === selectedCode;
            const areas = info ? info.areas : [{ name: loc.label, lat: loc.lat, lng: loc.lng }];

            areas.forEach((area) => {
                const marker = L.marker([area.lat, area.lng], {
                    icon: pinIcon(color, isSelected),
                    title: area.name,
                }).addTo(map);
                marker.bindTooltip(area.name, { direction: 'top', offset: [0, isSelected ? -44 : -31] });
                marker.on('click', () => { window.location.href = '/lokasi?lokasi=' + encodeURIComponent(slug); });
                allMarkers.push(marker);
            });
        });

        if (allMarkers.length) {
            map.fitBounds(L.featureGroup(allMarkers).getBounds(), { padding: [30, 30], maxZoom: 11 });
        }
    }
});