/**
 * Dashboard user
 *  - Grafik mengikuti data lokasi dari database (disiapkan DashboardController + TideChart)
 *  - Kartu, banner admin, dan grafik diperbarui otomatis tiap 15 detik
 */

const SLATE = '#64748b'; // penanda "saat ini"

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

function hexToRgba(hex, alpha) {
    const bigint = parseInt(hex.replace('#', ''), 16);
    const r = (bigint >> 16) & 255;
    const g = (bigint >> 8) & 255;
    const b = bigint & 255;
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

function getStatusColor(status) {
    return {
        aman: '#2563eb',
        waspada: '#d97706',
        bahaya: '#dc2626',
    }[status] || '#2563eb';
}

// Label nilai, garis putus-putus, dan badge jam di bawah sumbu X
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

                ctx.setLineDash([3, 3]);
                ctx.strokeStyle = '#cbd5e1';
                ctx.beginPath();
                ctx.moveTo(x, y);
                ctx.lineTo(x, scales.y.bottom);
                ctx.stroke();
                ctx.setLineDash([]);

                // Pasang di atas titik, surut di bawah titik; dibedakan lewat tulisan, bukan warna
                ctx.font = '700 12px Segoe UI, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillStyle = m.color;
                const prefix = { high: 'Pasang ', low: 'Surut ', now: 'Saat ini ' }[m.kind] || '';
                ctx.fillText(prefix + m.v.toFixed(2) + ' m', x, m.kind === 'low' ? y + 20 : y - 12);

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

document.addEventListener('DOMContentLoaded', function () {

    // ---------- Dropdown lokasi ----------
    const locationSelect = document.getElementById('locationSelect');
    const locationTrigger = document.getElementById('locationTrigger');
    const locationMenu = document.getElementById('locationMenu');

    if (locationSelect && locationTrigger && locationMenu) {
        locationTrigger.addEventListener('click', function (event) {
            event.stopPropagation();
            locationMenu.classList.toggle('is-open');
        });

        document.addEventListener('click', function (event) {
            if (!locationSelect.contains(event.target)) {
                locationMenu.classList.remove('is-open');
            }
        });
    } else {
        console.error('Elemen dropdown lokasi tidak ditemukan.');
    }

    // ---------- Grafik pasang surut ----------
    let currentData = window.TIDE_DATA
        ? window.TIDE_DATA[window.SELECTED_LOCATION]
        : null;

    let chartInstance = null;
    let lastSignature = null;

    const ctx = document.getElementById('tideChart');
    const chartCanvasWrap = ctx ? ctx.closest('.chart-card__canvas-wrap') : null;
    const chartFootnote = document.getElementById('chartFootnote');
    const chartIndicator = document.getElementById('chartLiveIndicator');
    const chartTitle = document.querySelector('.chart-card__title');

    // Baris keterangan (tanggal + sumber data) di bawah grafik; dibuat otomatis kalau belum ada
    function getChartInfo() {
        let el = document.getElementById('chartInfo');
        if (!el && chartCanvasWrap) {
            el = document.createElement('p');
            el.id = 'chartInfo';
            el.style.cssText = 'margin: 8px 0 0; font-size: 12.5px; color: #64748b;';
            chartCanvasWrap.insertAdjacentElement('afterend', el);
        }
        return el;
    }

    function showEmptyChart(message) {
        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }
        lastSignature = null;

        if (ctx) {
            ctx.style.display = 'none';
        }

        if (chartCanvasWrap) {
            chartCanvasWrap.style.display = 'flex';
            chartCanvasWrap.style.alignItems = 'center';
            chartCanvasWrap.style.justifyContent = 'center';
            chartCanvasWrap.style.minHeight = '260px';

            let emptyMessage = document.getElementById('chartEmptyMessage');

            if (!emptyMessage) {
                emptyMessage = document.createElement('p');
                emptyMessage.id = 'chartEmptyMessage';
                emptyMessage.style.textAlign = 'center';
                emptyMessage.style.color = '#64748b';
                emptyMessage.style.fontSize = '14px';
                emptyMessage.style.padding = '24px';
                chartCanvasWrap.appendChild(emptyMessage);
            }

            emptyMessage.textContent = message;
            emptyMessage.style.display = 'block';
        }

        if (chartTitle) chartTitle.textContent = 'Prediksi Pasang Surut Hari Ini';

        const info = getChartInfo();
        if (info) info.style.display = 'none';

        if (chartFootnote) chartFootnote.style.display = 'none';
        if (chartIndicator) chartIndicator.style.display = 'none';
    }

    // Judul, keterangan, dan kalimat "pasang tertinggi" mengikuti data grafik
    function updateChartTexts(data) {
        const c = (data && data.chart) || {};

        if (chartTitle) {
            chartTitle.textContent = c.date && !c.isToday
                ? 'Prediksi Pasang Surut ' + c.dateLabel
                : 'Prediksi Pasang Surut Hari Ini';
        }

        const info = getChartInfo();
        if (info) {
            let text = (c.dateLabel ? c.dateLabel + ' · ' : '') +
                (c.source === 'hourly'
                    ? 'Data tinggi air per jam'
                    : 'Kurva diperkirakan dari titik pasang dan surut harian');
            if (c.date && !c.isToday) {
                text += '. Belum ada data untuk hari ini, ditampilkan tanggal terdekat yang tersedia.';
            }
            info.textContent = text;
            info.style.display = '';
        }

        const footnoteText = document.getElementById('chartFootnoteText');
        if (footnoteText && c.high) {
            footnoteText.textContent =
                'Pasang tertinggi ' + (c.isToday ? 'hari ini' : c.dateLabel) + ': ' +
                Number(c.high.v).toFixed(2) + ' m pada pukul ' + c.high.time;
        }
    }

    function chartSignature(data) {
        const c = data.chart || {};
        const pts = c.points || [];
        const first = pts[0] || {};
        const last = pts[pts.length - 1] || {};
        return [
            data.status, c.date, c.source, pts.length, first.v, last.v,
            c.nowH, c.high && c.high.v, c.low && c.low.v,
        ].join('|');
    }

    function drawChart(data, animate) {
        const c = data.chart;
        const pts = c.points;
        const high = c.high;
        const low = c.low;
        const color = getStatusColor(data.status);
        const range = yRange(pts.map((p) => p.v));

        const markers = [];
        if (high) markers.push({ h: high.h, v: high.v, time: high.time, color: color, kind: 'high' });
        if (low) markers.push({ h: low.h, v: low.v, time: low.time, color: color, kind: 'low' });
        if (c.isToday && c.nowH !== null) {
            markers.push({ h: c.nowH, v: valueAt(pts, c.nowH), time: c.nowTime, color: SLATE, kind: 'now' });
        }

        const isExtreme = (p) =>
            (high && Math.abs(p.h - high.h) < 0.01 && Math.abs(p.v - high.v) < 0.011) ||
            (low && Math.abs(p.h - low.h) < 0.01 && Math.abs(p.v - low.v) < 0.011);

        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                datasets: [{
                    label: 'Ketinggian (m)',
                    data: pts.map((p) => ({ x: p.h, y: p.v })),
                    borderColor: color,
                    backgroundColor: hexToRgba(color, 0.12),
                    borderWidth: 2.5,
                    tension: 0.35,
                    fill: true,
                    pointRadius: (pc) => (isExtreme(pts[pc.dataIndex]) ? 5 : 0),
                    pointBackgroundColor: color,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }],
            },
            plugins: [makeMarkerPlugin(markers)],
            options: {
                animation: animate ? { duration: 400 } : false,
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
                        title: { display: true, text: 'Ketinggian (m)', color: '#94a3b8', font: { size: 11 } },
                        ticks: { stepSize: 0.5 },
                        grid: { color: '#f1f5f9' },
                    },
                },
            },
        });
    }

    function showChart(data) {
        if (!ctx) return;

        const points = data && data.chart && Array.isArray(data.chart.points)
            ? data.chart.points
            : [];

        if (!points.length) {
            const nama = data && data.label ? ' ' + data.label : ' lokasi ini';
            showEmptyChart('Belum ada data grafik pasang surut untuk' + nama + '. Grafik akan tampil otomatis setelah admin mengunggah datanya.');
            return;
        }

        ctx.style.display = 'block';

        if (chartCanvasWrap) {
            chartCanvasWrap.style.display = '';
            chartCanvasWrap.style.minHeight = '';
        }

        const emptyMessage = document.getElementById('chartEmptyMessage');
        if (emptyMessage) emptyMessage.style.display = 'none';

        if (chartFootnote) chartFootnote.style.display = '';
        if (chartIndicator) chartIndicator.style.display = '';

        updateChartTexts(data);

        // Gambar ulang hanya kalau datanya berubah (supaya grafik tidak berkedip tiap polling)
        const signature = chartSignature(data);
        if (signature === lastSignature && chartInstance) return;

        const animate = chartInstance === null;
        lastSignature = signature;
        drawChart(data, animate);
    }

    if (ctx) {
        showChart(currentData);
    }

    // ---------- Update DOM secara dinamis ----------
    function updateDOM(active, notification) {
        if (!active) return;

        // Stat cards
        const valCurrentTide = document.getElementById('valCurrentTide');
        if (valCurrentTide) valCurrentTide.textContent = Number(active.current_tide).toFixed(1) + ' m';

        const valCurrentTime = document.getElementById('valCurrentTime');
        if (valCurrentTime) valCurrentTime.textContent = active.current_time;

        const valStatusLabel = document.getElementById('valStatusLabel');
        if (valStatusLabel) {
            valStatusLabel.textContent = active.status_label;
            valStatusLabel.className = `stat-card__value status-text--${active.status}`;
        }

        const valStatusDesc = document.getElementById('valStatusDesc');
        if (valStatusDesc) valStatusDesc.textContent = active.status_desc;

        const iconStatus = document.getElementById('iconStatus');
        if (iconStatus) {
            iconStatus.className = `stat-card__icon stat-card__icon--status status--${active.status}`;
            if (active.status === 'aman') {
                iconStatus.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            } else {
                iconStatus.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 9v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>';
            }
        }

        const valWindSpeed = document.getElementById('valWindSpeed');
        if (valWindSpeed) valWindSpeed.textContent = active.wind_speed + ' km/jam';

        const valWindDir = document.getElementById('valWindDir');
        if (valWindDir) valWindDir.textContent = active.wind_dir;

        const valWeather = document.getElementById('valWeather');
        if (valWeather) valWeather.textContent = active.weather;

        const valTemperature = document.getElementById('valTemperature');
        if (valTemperature) valTemperature.innerHTML = active.temperature + '&deg;C';

        // Side info card
        const valUpdatedAt = document.getElementById('valUpdatedAt');
        if (valUpdatedAt) valUpdatedAt.textContent = active.updated_at;

        const valLastHeight = document.getElementById('valLastHeight');
        if (valLastHeight) valLastHeight.textContent = Number(active.last_height).toFixed(1) + ' m';

        const valCoordinate = document.getElementById('valCoordinate');
        if (valCoordinate) valCoordinate.textContent = active.coordinate;

        // Warning card
        const warningCard = document.getElementById('warningCard');
        if (warningCard) {
            warningCard.className = `warning-card warning-card--${active.status}`;
        }

        const valWarningTitle = document.getElementById('valWarningTitle');
        if (valWarningTitle) valWarningTitle.textContent = active.warning_title;

        const valWarningDesc = document.getElementById('valWarningDesc');
        if (valWarningDesc) valWarningDesc.textContent = active.warning_desc;

        // Banner notifikasi admin
        const liveNotifBanner = document.getElementById('liveNotifBanner');
        const liveNotifTitle = document.getElementById('liveNotifTitle');
        const liveNotifMsg = document.getElementById('liveNotifMsg');
        if (liveNotifBanner && liveNotifTitle && liveNotifMsg) {
            if (notification && notification.title) {
                liveNotifTitle.textContent = notification.title;
                liveNotifMsg.textContent = notification.message || '';
                liveNotifBanner.className = `alert-banner alert-banner--${notification.type || 'info'}`;
                liveNotifBanner.style.display = 'flex';
            } else {
                liveNotifBanner.style.display = 'none';
            }
        }

        // Time badge
        const liveTimeBadge = document.getElementById('liveTimeBadge');
        if (liveTimeBadge) {
            const now = new Date();
            const hh = String(now.getHours()).padStart(2, '0');
            const mm = String(now.getMinutes()).padStart(2, '0');
            liveTimeBadge.textContent = `${hh}:${mm} WIB`;
        }

        // Grafik (judul, keterangan, dan kalimat pasang tertinggi ikut diperbarui di dalamnya)
        currentData = active;
        showChart(active);
    }

    // ---------- Realtime polling (auto-update tiap 15 detik) ----------
    const realtimeUrl = window.REALTIME_URL || '/dashboard/realtime';

    function fetchRealtimeData() {
        const url = `${realtimeUrl}?lokasi=${encodeURIComponent(window.SELECTED_LOCATION || 'surabaya-timur')}&_t=${Date.now()}`;
        return fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then((res) => res.json())
        .then((res) => {
            if (res.status === 'success' && res.active) {
                updateDOM(res.active, res.notification);

                const liveStatusText = document.getElementById('liveStatusText');
                if (liveStatusText) {
                    liveStatusText.textContent = 'Terupdate Barusan';
                    setTimeout(() => {
                        liveStatusText.textContent = 'Live Data Terhubung';
                    }, 2500);
                }
            }
        })
        .catch((err) => {
            console.warn('[SIPASUT] Polling update error:', err);
        });
    }

    setInterval(fetchRealtimeData, 15000);
});