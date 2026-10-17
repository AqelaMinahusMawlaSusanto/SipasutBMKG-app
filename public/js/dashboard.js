document.addEventListener('DOMContentLoaded', () => {
    // ---------- Dropdown lokasi buka/tutup ----------
    const wrap = document.getElementById('locationSelect');
    const trigger = document.getElementById('locationTrigger');

    if (trigger && wrap) {
        trigger.addEventListener('click', () => {
            wrap.classList.toggle('is-open');
        });

        document.addEventListener('click', (e) => {
            if (!wrap.contains(e.target)) {
                wrap.classList.remove('is-open');
            }
        });
    }

    // ---------- Chart pasang surut Initialization ----------
    let currentData = window.TIDE_DATA ? window.TIDE_DATA[window.SELECTED_LOCATION] : null;
    let chartInstance = null;

    const ctx = document.getElementById('tideChart');
    if (ctx && currentData) {
        initChart(currentData);
    }

    function getStatusColor(status) {
        return {
            aman: '#2563eb',
            waspada: '#d97706',
            bahaya: '#dc2626',
        }[status] || '#2563eb';
    }

    function initChart(data) {
        const statusColor = getStatusColor(data.status);
        const points = [...data.chart.points].sort((a, b) => a.time.localeCompare(b.time));
        const labels = points.map(p => p.time);
        const values = points.map(p => p.value);

        if (chartInstance) {
            chartInstance.destroy();
        }

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Ketinggian (m)',
                    data: values,
                    borderColor: statusColor,
                    backgroundColor: hexToRgba(statusColor, 0.12),
                    borderWidth: 2.5,
                    tension: 0.45,
                    fill: true,
                    pointRadius: 5,
                    pointBackgroundColor: statusColor,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (item) => `${item.formattedValue} m pada ${item.label}`,
                        },
                    },
                    datalabels: false,
                },
                scales: {
                    y: {
                        title: { display: true, text: 'Ketinggian (m)', color: '#94a3b8', font: { size: 11 } },
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: '#f1f5f9' },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
            },
        });
    }

    function hexToRgba(hex, alpha) {
        const bigint = parseInt(hex.replace('#', ''), 16);
        const r = (bigint >> 16) & 255;
        const g = (bigint >> 8) & 255;
        const b = bigint & 255;
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    // ---------- Update DOM secara Dinamis ----------
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

        // Chart Footnote
        const chartFootnoteText = document.getElementById('chartFootnoteText');
        if (chartFootnoteText && active.next_high) {
            if (active.status === 'bahaya') {
                chartFootnoteText.textContent = `Potensi pasang tertinggi diperkirakan terjadi pada pukul ${active.next_high.time}`;
            } else if (active.status === 'waspada') {
                chartFootnoteText.textContent = `Prediksi pasang tinggi diperkirakan terjadi pada pukul ${active.next_high.time}`;
            } else {
                chartFootnoteText.textContent = `Prediksi pasang tertinggi berikutnya ${Number(active.next_high.value).toFixed(1)} m pada pukul ${active.next_high.time}`;
            }
        }

        // Notification Banner
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

        // Update chart jika data titik atau status berubah
        if (active.chart && active.chart.points) {
            initChart(active);
        }
    }

    // ---------- Realtime Polling (Auto-Update setiap 15 detik) ----------
    const realtimeUrl = window.REALTIME_URL || '/dashboard/realtime';

    function fetchRealtimeData() {
        const url = `${realtimeUrl}?lokasi=${encodeURIComponent(window.SELECTED_LOCATION || 'surabaya-timur')}&_t=${Date.now()}`;
        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success' && res.active) {
                updateDOM(res.active, res.notification);
                
                // Visual flash indicator
                const liveStatusText = document.getElementById('liveStatusText');
                if (liveStatusText) {
                    liveStatusText.textContent = 'Terupdate Barusan';
                    setTimeout(() => {
                        liveStatusText.textContent = 'Live Data Terhubung';
                    }, 2500);
                }
            }
        })
        .catch(err => {
            console.warn('[SIPASUT] Polling update error:', err);
        });
    }

    // Poll setiap 15 detik
    setInterval(fetchRealtimeData, 15000);
});
