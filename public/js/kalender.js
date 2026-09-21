document.addEventListener('DOMContentLoaded', function () {

    var BULAN = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    var HARI  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    // ---------- State ----------
    var today = new Date();
    var viewYear  = today.getFullYear();
    var viewMonth = today.getMonth(); // 0-11
    var selectedDate = null;          // Date object
    var selectedLocation = null;      // { slug, name }

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
        // Senin = 0 ... Minggu = 6
        var startOffset = (firstDay.getDay() + 6) % 7;

        var totalDaysThisMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
        var totalDaysPrevMonth = new Date(viewYear, viewMonth, 0).getDate();

        var cells = [];

        // Tanggal sisa bulan sebelumnya (abu-abu)
        for (var i = startOffset; i > 0; i--) {
            cells.push({ day: totalDaysPrevMonth - i + 1, outside: true });
        }
        // Tanggal bulan ini
        for (var d = 1; d <= totalDaysThisMonth; d++) {
            cells.push({ day: d, outside: false });
        }
        // Genapkan ke kelipatan 7 dengan tanggal bulan berikutnya
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

    // ---------- Data pasang/surut dummy (deterministik per lokasi+tanggal) ----------
    function hashSeed(str) {
        var hash = 0;
        for (var i = 0; i < str.length; i++) {
            hash = (hash * 31 + str.charCodeAt(i)) >>> 0;
        }
        return hash;
    }

    function pad(n) { return n < 10 ? '0' + n : '' + n; }

    function computeTide(slug, dateObj) {
        var dateKey = dateObj.getFullYear() + '-' + pad(dateObj.getMonth() + 1) + '-' + pad(dateObj.getDate());
        var seed = hashSeed(slug + '|' + dateKey);

        var pasangHour   = 8 + (seed % 5);            // 08 - 12
        var pasangMinute = [0, 15, 30, 45][seed % 4];
        var pasangTinggi = (1.2 + ((seed >> 3) % 100) / 100).toFixed(2); // 1.20 - 2.19

        var surutSeed    = seed >> 5;
        var surutHour    = 14 + (surutSeed % 4);       // 14 - 17
        var surutMinute  = [0, 15, 30, 45][surutSeed % 4];
        var surutTinggi  = (0.2 + ((surutSeed >> 3) % 70) / 100).toFixed(2); // 0.20 - 0.89

        return {
            pasang: {
                waktu: pad(pasangHour) + '.' + pad(pasangMinute) + ' WIB',
                tinggi: pasangTinggi.replace('.', ',') + ' CM',
                status: parseFloat(pasangTinggi) >= 2.0 ? 'Waspada' : 'Aman',
            },
            surut: {
                waktu: pad(surutHour) + '.' + pad(surutMinute) + ' WIB',
                tinggi: surutTinggi.replace('.', ',') + ' CM',
                status: parseFloat(surutTinggi) <= 0.25 ? 'Waspada' : 'Aman',
            },
        };
    }

    function tideFieldHtml(label, value, status) {
        var statusHtml = status
            ? '<span class="tide-status' + (status === 'Waspada' ? ' is-waspada' : '') + '">' + status + '</span>'
            : '';
        return '<div class="tide-field">' +
                    '<span class="tide-field__label">' + label + '</span>' +
                    '<span class="tide-field__value">' + value + '</span>' +
               '</div>' + statusHtml;
    }

    function renderDetail() {
        if (!selectedLocation || !selectedDate) {
            detailDateLabel.hidden = true;
            pasangBody.innerHTML = '';
            surutBody.innerHTML = '';
            pasangBody.classList.add('is-empty');
            surutBody.classList.add('is-empty');
            return;
        }

        var tide = computeTide(selectedLocation.slug, selectedDate);

        detailDateLabel.hidden = false;
        detailDateLabel.textContent = HARI[selectedDate.getDay()] + ', ' +
            selectedDate.getDate() + ' ' + BULAN[selectedDate.getMonth()] + ' ' + selectedDate.getFullYear();

        pasangBody.classList.remove('is-empty');
        surutBody.classList.remove('is-empty');

        pasangBody.innerHTML =
            tideFieldHtml('&#128337; Waktu', tide.pasang.waktu) +
            tideFieldHtml('&#127754; Tinggi', tide.pasang.tinggi) +
            '<div class="tide-field"><span class="tide-field__label">Status</span>' +
            '<span class="tide-status' + (tide.pasang.status === 'Waspada' ? ' is-waspada' : '') + '">' + tide.pasang.status + '</span></div>';

        surutBody.innerHTML =
            tideFieldHtml('&#128337; Waktu', tide.surut.waktu) +
            tideFieldHtml('&#127754; Tinggi', tide.surut.tinggi) +
            '<div class="tide-field"><span class="tide-field__label">Status</span>' +
            '<span class="tide-status' + (tide.surut.status === 'Waspada' ? ' is-waspada' : '') + '">' + tide.surut.status + '</span></div>';
    }

    // ---------- Init ----------
    // Tanggal & lokasi belum dipilih otomatis; kartu PASANG/SURUT kosong
    // sampai user memilih lokasi lalu mengklik tanggal di kalender.
    renderCalendar();
    renderDetail();
});