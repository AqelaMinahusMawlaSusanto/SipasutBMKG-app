document.addEventListener('DOMContentLoaded', () => {

    // ---------- Toggle 24 Jam / 7 Hari (tampilan saja untuk saat ini) ----------
    document.querySelectorAll('.range-toggle__btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.range-toggle__btn').forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            // TODO: ganti data chart ke rentang 7 hari kalau data mingguan sudah tersedia
        });
    });

    // ---------- Grafik pasang surut (dua warna: Pasang biru, Surut oranye) ----------
    const ctx = document.getElementById('tideDetailChart');
    if (ctx) {
        // Titik data 24 jam (jam desimal, tinggi meter) sesuai contoh di desain
        const points = [
            { h: 0,     v: 0.30 },
            { h: 2.75,  v: 1.10, label: '1.10 m' },
            { h: 5.5,   v: -0.20 },
            { h: 8.73,  v: 0.61, label: '0.61 m', badge: '09.00 WIB', current: true },
            { h: 11.5,  v: 0.30 },
            { h: 14.28, v: 1.20, label: '1.20 m', badge: '14.30 WIB' },
            { h: 17,    v: 0.40 },
            { h: 19.5,  v: -0.30, label: '-0.30 m', badge: '20.15 WIB' },
            { h: 22,    v: -0.10 },
            { h: 24,    v: 0.30 },
        ];

        // Indeks tempat warna kurva berpindah dari biru (pasang) ke oranye (surut)
        const colorSwitchIndex = points.findIndex(p => p.h >= 14.28);

        const toTimeLabel = (h) => {
            const hh = Math.floor(h) % 24;
            const mm = Math.round((h % 1) * 60);
            return `${String(hh).padStart(2, '0')}:${String(mm).padStart(2, '0')}`;
        };

        // Plugin custom untuk gambar label angka & badge waktu di titik tertentu
        const pointLabelPlugin = {
            id: 'pointLabelPlugin',
            afterDatasetsDraw(chart) {
                const { ctx, scales } = chart;
                points.forEach((p) => {
                    if (!p.label) return;
                    const x = scales.x.getPixelForValue(toTimeLabel(p.h));
                    const y = scales.y.getPixelForValue(p.v);
                    const color = p.current ? '#2563eb' : (p.v === 1.20 ? '#16a34a' : (p.v < 0 ? '#f97316' : '#2563eb'));

                    ctx.save();
                    ctx.font = '700 12px Segoe UI, sans-serif';
                    ctx.fillStyle = color;
                    ctx.textAlign = 'center';
                    ctx.fillText(p.label, x, y - 14);

                    if (p.badge) {
                        ctx.setLineDash([3, 3]);
                        ctx.strokeStyle = '#cbd5e1';
                        ctx.beginPath();
                        ctx.moveTo(x, y);
                        ctx.lineTo(x, scales.y.getPixelForValue(scales.y.min) + 18);
                        ctx.stroke();

                        const label = p.badge;
                        const w = ctx.measureText(label).width + 16;
                        const boxY = scales.y.getPixelForValue(scales.y.min) + 20;
                        ctx.setLineDash([]);
                        ctx.fillStyle = color;
                        ctx.fillRect(x - w / 2, boxY, w, 20);
                        ctx.fillStyle = '#fff';
                        ctx.font = '700 11px Segoe UI, sans-serif';
                        ctx.fillText(label, x, boxY + 14);
                    }
                    ctx.restore();
                });
            },
        };

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: points.map(p => toTimeLabel(p.h)),
                datasets: [{
                    data: points.map(p => p.v),
                    borderWidth: 2.5,
                    tension: 0.4,
                    pointRadius: (c) => points[c.dataIndex]?.label ? 5 : 0,
                    pointBackgroundColor: (c) => {
                        const p = points[c.dataIndex];
                        if (p.current) return '#2563eb';
                        if (p.v === 1.20) return '#16a34a';
                        if (p.v < 0) return '#f97316';
                        return '#2563eb';
                    },
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    fill: true,
                    backgroundColor: (c) => {
                        const { ctx: cctx, chartArea } = c.chart;
                        if (!chartArea) return 'rgba(37,99,235,.08)';
                        const g = cctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        g.addColorStop(0, 'rgba(37,99,235,.15)');
                        g.addColorStop(1, 'rgba(37,99,235,0)');
                        return g;
                    },
                    segment: {
                        borderColor: (c) => (c.p1DataIndex >= colorSwitchIndex ? '#f97316' : '#2563eb'),
                    },
                }],
            },
            plugins: [pointLabelPlugin],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { bottom: 26 } },
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: (i) => `${i.formattedValue} m` } } },
                scales: {
                    y: {
                        title: { display: true, text: 'Tinggi (m)', color: '#94a3b8', font: { size: 11 } },
                        min: -1.5, max: 1.5, ticks: { stepSize: 0.5 },
                        grid: { color: '#f1f5f9' },
                    },
                    x: { grid: { display: false } },
                },
            },
        });
    }

    // ---------- Peta lokasi monitoring (Leaflet + OpenStreetMap) ----------
    const mapEl = document.getElementById('locationMap');
    if (mapEl && window.L && window.MAP_LOCATIONS) {
        const colorFor = { aman: '#2563eb', waspada: '#f97316', bahaya: '#dc2626' };
        const pinIcon = (color) => L.divIcon({
            className: '',
            html: `<svg width="26" height="34" viewBox="0 0 24 34"><path d="M12 0C5.4 0 0 5.4 0 12c0 9 12 22 12 22s12-13 12-22C24 5.4 18.6 0 12 0z" fill="${color}"/><circle cx="12" cy="12" r="5" fill="#fff"/></svg>`,
            iconSize: [26, 34],
            iconAnchor: [13, 34],
        });

        const map = L.map('locationMap', { scrollWheelZoom: false }).setView([-7.4, 113.2], 7);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const markers = [];
        window.MAP_LOCATIONS.forEach(loc => {
            const marker = L.marker([loc.lat, loc.lng], { icon: pinIcon(colorFor[loc.status] || '#2563eb') })
                .addTo(map)
                .bindTooltip(loc.label, { permanent: false, direction: 'top' });
            markers.push(marker);
        });

        if (markers.length) {
            const group = L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.3));
        }
    }
});