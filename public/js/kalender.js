document.addEventListener('DOMContentLoaded', function () {

    var BULAN = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    var HARI  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    // ---------- State ----------
    var today = new Date();
    var viewYear  = today.getFullYear();
    var viewMonth = today.getMonth(); // 0-11
    var selectedDate = null;
    var selectedLocation = null;      // { slug, name }
    var latestRequest = null;

    // ---------- Elemen ----------
    var selectControl = document.getElementById('kalenderSelectControl');
    var selectList     = document.getElementById('kalenderSelectList');
    var selectValueEl  = document.getElementById('kalenderSelectValue');
    var selectChevron  = document.getElementById('kalenderSelectChevron');

    var monthYearLabel = document.getElementById('monthYearLabel');
    var monthYearText  = document.getElementById('monthYearText');
    var daysContainer  = document.getElementById('kalenderDays');
    var prevMonthBtn   = document.getElementById('prevMonth');
    var nextMonthBtn   = document.getElementById('nextMonth');

    var jumpPanel  = document.getElementById('kalenderJump');
    var jumpMonth  = document.getElementById('jumpMonth');
    var jumpYear   = document.getElementById('jumpYear');
    var jumpYearPrev = document.getElementById('jumpYearPrev');
    var jumpYearNext = document.getElementById('jumpYearNext');

    var detailDateLabel = document.getElementById('detailDateLabel');
    var pasangBody = document.getElementById('pasangBody');
    var surutBody  = document.getElementById('surutBody');

    // ---------- Dropdown lokasi ----------
    selectControl.addEventListener('click', function () {
        var isHidden = selectList.hasAttribute('hidden');
        if (isHidden) {
            selectList.removeAttribute('hidden');
        } else {
            selectList.setAttribute('hidden', '');
        }
        selectChevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
    });

    selectList.querySelectorAll('.kalender-select__item').forEach(function (item) {
        item.addEventListener('click', function () {
            selectList.querySelectorAll('.kalender-select__item').forEach(function (i) {
                i.classList.remove('is-active');
            });
            item.classList.add('is-active');

            selectedLocation = { slug: item.dataset.slug, name: item.dataset.name };
            selectValueEl.textContent = selectedLocation.name;
            selectValueEl.classList.remove('is-placeholder');

            selectList.setAttribute('hidden', '');
            selectChevron.style.transform = 'rotate(0deg)';

            renderDetail();
        });
    });

    document.addEventListener('click', function (e) {
        if (!document.getElementById('kalenderSelect').contains(e.target)) {
            selectList.setAttribute('hidden', '');
            selectChevron.style.transform = 'rotate(0deg)';
        }
    });

    // ---------- Panel ganti bulan/tahun cepat ----------
    for (var m = 0; m < 12; m++) {
        var opt = document.createElement('option');
        opt.value = m;
        opt.textContent = BULAN[m];
        jumpMonth.appendChild(opt);
    }
    for (var y = today.getFullYear() - 5; y <= today.getFullYear() + 5; y++) {
        var optY = document.createElement('option');
        optY.value = y;
        optY.textContent = y;
        jumpYear.appendChild(optY);
    }

    monthYearLabel.addEventListener('click', function () {
        jumpMonth.value = viewMonth;
        jumpYear.value = viewYear;
        jumpPanel.hidden = !jumpPanel.hidden;
    });

    jumpMonth.addEventListener('change', function () {
        viewMonth = parseInt(jumpMonth.value, 10);
        renderCalendar();
    });

    jumpYear.addEventListener('change', function () {
        viewYear = parseInt(jumpYear.value, 10);
        renderCalendar();
    });

    jumpYearPrev.addEventListener('click', function () {
        jumpYear.value = parseInt(jumpYear.value, 10) - 1;
        viewYear = parseInt(jumpYear.value, 10);
        renderCalendar();
    });

    jumpYearNext.addEventListener('click', function () {
        jumpYear.value = parseInt(jumpYear.value, 10) + 1;
        viewYear = parseInt(jumpYear.value, 10);
        renderCalendar();
    });

    // ---------- Navigasi bulan ----------
    prevMonthBtn.addEventListener('click', function () {
        viewMonth--;
        if (viewMonth < 0) { viewMonth = 11; viewYear--; }
        renderCalendar();
    });

    nextMonthBtn.addEventListener('click', function () {
        viewMonth++;
        if (viewMonth > 11) { viewMonth = 0; viewYear++; }
        renderCalendar();
    });

    // ---------- Render kalender ----------
    function renderCalendar() {
        monthYearText.textContent = BULAN[viewMonth] + ' ' + viewYear;
        jumpPanel.hidden = true;
        daysContainer.innerHTML = '';

        var firstDay = new Date(viewYear, viewMonth, 1);
        var startOffset = (firstDay.getDay() + 6) % 7; // Senin = 0

        var totalDaysThisMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
        var totalDaysPrevMonth = new Date(viewYear, viewMonth, 0).getDate();

        var cells = [];
        for (var i = startOffset; i > 0; i--) {
            cells.push({ day: totalDaysPrevMonth - i + 1, outside: true });
        }
        for (var d = 1; d <= totalDaysThisMonth; d++) {
            cells.push({ day: d, outside: false });
        }
        var nextDay = 1;
        while (cells.length % 7 !== 0) {
            cells.push({ day: nextDay++, outside: true });
        }

        cells.forEach(function (cell) {
            var el = document.createElement('div');
            el.className = 'kalender-day' + (cell.outside ? ' is-outside' : '');
            el.textContent = cell.day;

            if (!cell.outside) {
                var thisDate = new Date(viewYear, viewMonth, cell.day);
                if (selectedDate &&
                    thisDate.getFullYear() === selectedDate.getFullYear() &&
                    thisDate.getMonth() === selectedDate.getMonth() &&
                    thisDate.getDate() === selectedDate.getDate()) {
                    el.classList.add('is-selected');
                }
                el.addEventListener('click', function () {
                    selectedDate = new Date(viewYear, viewMonth, cell.day);
                    renderCalendar();
                    renderDetail();
                });
            }

            daysContainer.appendChild(el);
        });
    }

    // ---------- Data pasang/surut dari admin (tabel database) ----------
    function pad(n) { return n < 10 ? '0' + n : '' + n; }

    function dateKey(d) {
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }

    // Tanpa cache: tiap pilih tanggal/lokasi, data diambil ulang,
    // jadi perubahan dari admin langsung ikut tampil.
    function fetchTide(slug, year, month) {
        var url = window.KALENDER_DATA_URL + '?slug=' + encodeURIComponent(slug) +
                  '&tahun=' + year + '&bulan=' + month;
        return fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (r) {
                if (!r.ok) throw new Error(r.status);
                return r.json();
            });
    }

    function tideFieldHtml(label, value) {
        return '<div class="tide-field">' +
                   '<span class="tide-field__label">' + label + '</span>' +
                   '<span class="tide-field__value">' + value + '</span>' +
               '</div>';
    }

    function setBodies(text, isEmpty) {
        pasangBody.innerHTML = text;
        surutBody.innerHTML = text;
        pasangBody.classList.toggle('is-empty', isEmpty);
        surutBody.classList.toggle('is-empty', isEmpty);
    }

    function renderDetail() {
        if (!selectedLocation || !selectedDate) {
            detailDateLabel.hidden = true;
            setBodies('', true);
            return;
        }

        detailDateLabel.hidden = false;
        detailDateLabel.textContent = HARI[selectedDate.getDay()] + ', ' +
            selectedDate.getDate() + ' ' + BULAN[selectedDate.getMonth()] + ' ' + selectedDate.getFullYear();

        var requestKey = selectedLocation.slug + '|' + dateKey(selectedDate);
        latestRequest = requestKey;
        setBodies('Memuat data...', true);

        fetchTide(selectedLocation.slug, selectedDate.getFullYear(), selectedDate.getMonth() + 1)
            .then(function (data) {
                if (latestRequest !== requestKey) return; // pilihan sudah berganti
                var tide = data[dateKey(selectedDate)];

                if (!tide) {
                    setBodies('Belum ada data untuk tanggal ini.', true);
                    return;
                }

                var statusClass = tide.status === 'Aman' ? '' : ' is-waspada';

                pasangBody.classList.remove('is-empty');
                surutBody.classList.remove('is-empty');

                pasangBody.innerHTML =
                    tideFieldHtml('&#128337; Waktu', tide.jam_pasang) +
                    tideFieldHtml('&#127754; Tinggi', tide.tinggi_pasang) +
                    '<div class="tide-field"><span class="tide-field__label">Status</span>' +
                    '<span class="tide-status' + statusClass + '">' + tide.status + '</span></div>';

                surutBody.innerHTML =
                    tideFieldHtml('&#128337; Waktu', tide.jam_surut) +
                    tideFieldHtml('&#127754; Tinggi', tide.tinggi_surut);
            })
            .catch(function () {
                if (latestRequest !== requestKey) return;
                setBodies('Gagal memuat data.', true);
            });
    }

    // ---------- Init ----------
    renderCalendar();
    renderDetail();
});