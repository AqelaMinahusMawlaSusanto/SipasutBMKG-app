document.addEventListener('DOMContentLoaded', () => {
    // ---------- Dropdown lokasi buka/tutup ----------
    const wrap = document.getElementById('locationSelect');
    const trigger = document.getElementById('locationTrigger');

    if (trigger) {
        trigger.addEventListener('click', () => {
            wrap.classList.toggle('is-open');
        });

        document.addEventListener('click', (e) => {
            if (!wrap.contains(e.target)) {
                wrap.classList.remove('is-open');
            }
        });
    }

    // ---------- Chart pasang surut ----------
    const active = window.TIDE_DATA[window.SELECTED_LOCATION];
    if (!active) return;

    const statusColor = {
        aman: '#2563eb',
        waspada: '#d97706',
        bahaya: '#dc2626',
    }[active.status] || '#2563eb';

    // Susun titik data jam:nilai lalu urutkan berdasarkan waktu
    const points = [...active.chart.points].sort((a, b) => a.time.localeCompare(b.time));
    const labels = points.map(p => p.time);
    const values = points.map(p => p.value);

    const ctx = document.getElementById('tideChart');
    if (!ctx) return;

    new Chart(ctx, {
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

    function hexToRgba(hex, alpha) {
        const bigint = parseInt(hex.replace('#', ''), 16);
        const r = (bigint >> 16) & 255;
        const g = (bigint >> 8) & 255;
        const b = bigint & 255;
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }
});
