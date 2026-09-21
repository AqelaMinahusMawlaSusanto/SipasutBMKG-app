document.addEventListener('DOMContentLoaded', function () {

    // Data ini dikirim dari resources/views/lokasi.blade.php lewat window.LOKASI_DATA
    var LOCATIONS = window.LOKASI_DATA || [];
    var selectedSlug = window.LOKASI_SELECTED || (LOCATIONS[0] && LOCATIONS[0].slug);

    if (!LOCATIONS.length) {
        return;
    }

    var STATUS_COLOR = {
        tinggi: '#dc2626',
        normal: '#16a34a',
        rendah: '#2563eb',
    };

    // ---------- 1. Inisialisasi peta ----------
    var defaultLoc = findLocation(selectedSlug) || LOCATIONS[0];

    var map = L.map('lokasiMap', {
        zoomControl: true,
    }).setView([defaultLoc.lat, defaultLoc.lng], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    // ---------- 2. Buat marker untuk tiap lokasi ----------
    var markers = {};

    function bigPinIcon(color) {
        var svg =
            '<svg width="34" height="46" viewBox="0 0 34 46" xmlns="http://www.w3.org/2000/svg">' +
            '<path d="M17 0C7.6 0 0 7.6 0 17c0 12.7 17 29 17 29s17-16.3 17-29C34 7.6 26.4 0 17 0z" fill="' + color + '"/>' +
            '<circle cx="17" cy="17" r="7" fill="#fff"/>' +
            '</svg>';
        return L.divIcon({
            html: '<div class="lokasi-pin">' + svg + '</div>',
            className: '',
            iconSize: [34, 46],
            iconAnchor: [17, 46],
            popupAnchor: [0, -40],
        });
    }

    function smallDotIcon(color) {
        return L.divIcon({
            html: '<div class="lokasi-dot" style="width:16px;height:16px;background:' + color + '"></div>',
            className: '',
            iconSize: [16, 16],
            iconAnchor: [8, 8],
            popupAnchor: [0, -8],
        });
    }

    LOCATIONS.forEach(function (loc) {
        var color = STATUS_COLOR[loc.status] || STATUS_COLOR.normal;
        var isSelected = loc.slug === selectedSlug;

        var marker = L.marker([loc.lat, loc.lng], {
            icon: isSelected ? bigPinIcon(color) : smallDotIcon(color),
            title: loc.name,
        }).addTo(map);

        marker.bindTooltip(loc.name, { direction: 'top', offset: [0, isSelected ? -40 : -10] });

        marker.on('click', function () {
            selectLocation(loc.slug, { fly: true });
        });

        markers[loc.slug] = marker;
    });

    // ---------- 3. Fungsi bantu ----------
    function findLocation(slug) {
        for (var i = 0; i < LOCATIONS.length; i++) {
            if (LOCATIONS[i].slug === slug) return LOCATIONS[i];
        }
        return null;
    }

    function refreshMarkerIcons() {
        LOCATIONS.forEach(function (loc) {
            var color = STATUS_COLOR[loc.status] || STATUS_COLOR.normal;
            var isSelected = loc.slug === selectedSlug;
            markers[loc.slug].setIcon(isSelected ? bigPinIcon(color) : smallDotIcon(color));
        });
    }

    function renderDetail(loc) {
        document.getElementById('detailImage').src = loc.image;
        document.getElementById('detailImage').alt = loc.name;
        document.getElementById('detailName').textContent = loc.name;
        document.getElementById('detailKabupaten').textContent = loc.kabupaten;
        document.getElementById('detailKoordinat').textContent =
            loc.lat.toFixed(4) + ', ' + loc.lng.toFixed(4);
        document.getElementById('detailIdLokasi').textContent = loc.id_lokasi;
        document.getElementById('detailJenisLokasi').textContent = loc.jenis_lokasi;
        document.getElementById('detailSensor').textContent = loc.sensor_update;

        var badge = document.getElementById('detailBadge');
        badge.textContent = loc.status_label;
        badge.className = 'badge badge--' + loc.status;
    }

    function selectLocation(slug, opts) {
        opts = opts || {};
        var loc = findLocation(slug);
        if (!loc) return;

        selectedSlug = slug;
        renderDetail(loc);
        refreshMarkerIcons();

        var input = document.getElementById('lokasiSearchInput');
        input.value = loc.name + ', ' + loc.kabupaten;

        if (opts.fly) {
            map.flyTo([loc.lat, loc.lng], 11, { duration: 0.6 });
        }

        closeDropdown();
    }

    // ---------- 4. Pencarian & dropdown ----------
    var searchWrap = document.getElementById('lokasiSearch');
    var searchInput = document.getElementById('lokasiSearchInput');
    var dropdown = document.getElementById('lokasiDropdown');
    var dropdownList = document.getElementById('lokasiDropdownList');

    function openDropdown(list) {
        dropdownList.innerHTML = '';

        if (!list.length) {
            var empty = document.createElement('li');
            empty.className = 'lokasi-dropdown__empty';
            empty.textContent = 'Lokasi tidak ditemukan';
            dropdownList.appendChild(empty);
        } else {
            list.forEach(function (loc) {
                var li = document.createElement('li');
                li.className = 'lokasi-dropdown__item' + (loc.slug === selectedSlug ? ' is-active' : '');
                li.innerHTML =
                    '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="margin-top:2px;flex-shrink:0;">' +
                    '<path d="M12 22s7-7.2 7-12.5A7 7 0 0 0 5 9.5C5 14.8 12 22 12 22z" stroke="' +
                    (loc.slug === selectedSlug ? '#2563eb' : '#94a3b8') +
                    '" stroke-width="2"/><circle cx="12" cy="9.5" r="2.3" fill="' +
                    (loc.slug === selectedSlug ? '#2563eb' : '#94a3b8') + '"/></svg>' +
                    '<span><strong>' + loc.name + '</strong><small>' + loc.kabupaten + '</small></span>';

                li.addEventListener('click', function () {
                    selectLocation(loc.slug, { fly: true });
                });

                dropdownList.appendChild(li);
            });
        }

        dropdown.hidden = false;
    }

    function closeDropdown() {
        dropdown.hidden = true;
    }

    function filterLocations(keyword) {
        keyword = (keyword || '').toLowerCase().trim();
        if (!keyword) return LOCATIONS;

        return LOCATIONS.filter(function (loc) {
            return (
                loc.name.toLowerCase().indexOf(keyword) !== -1 ||
                loc.kabupaten.toLowerCase().indexOf(keyword) !== -1
            );
        });
    }

    searchInput.addEventListener('focus', function () {
        openDropdown(filterLocations(searchInput.value));
    });

    searchInput.addEventListener('input', function () {
        openDropdown(filterLocations(searchInput.value));
    });

    // Klik di luar kolom pencarian -> tutup dropdown
    document.addEventListener('click', function (e) {
        if (!searchWrap.contains(e.target)) {
            closeDropdown();
        }
    });

    document.getElementById('btnCari').addEventListener('click', function () {
        var results = filterLocations(searchInput.value);
        if (results.length === 1) {
            selectLocation(results[0].slug, { fly: true });
        } else {
            openDropdown(results);
        }
    });

    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btnCari').click();
        }
    });

    // ---------- 5. Toggle legenda "Kondisi Air" ----------
    var legendToggle = document.getElementById('legendToggle');
    var legendBox = document.getElementById('legendBox');

    legendToggle.addEventListener('click', function () {
        var isOpen = !legendBox.hidden;
        legendBox.hidden = isOpen;
        legendToggle.setAttribute('aria-expanded', String(!isOpen));
    });

    // ---------- 6. Tampilan awal ----------
    renderDetail(defaultLoc);
    searchInput.value = defaultLoc.name + ', ' + defaultLoc.kabupaten;
});
