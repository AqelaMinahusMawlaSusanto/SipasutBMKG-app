import os
import re
import csv
import json
import pandas as pd
from typing import List, Dict, Any, Optional

MONTH_MAP = {
    'januari': 1, 'februari': 2, 'maret': 3, 'april': 4, 'mei': 5, 'juni': 6,
    'juli': 7, 'agustus': 8, 'september': 9, 'oktober': 10, 'november': 11, 'desember': 12,
    'jan': 1, 'feb': 2, 'mar': 3, 'apr': 4, 'may': 5, 'jun': 6,
    'jul': 7, 'aug': 8, 'sep': 9, 'oct': 10, 'nov': 11, 'dec': 12
}

def is_yellow_color(color: Any) -> bool:
    """Deteksi warna kuning (highlight pasang/surut BMKG)"""
    if not color:
        return False
    if isinstance(color, (tuple, list)):
        if len(color) == 3:  # RGB (0..1)
            r, g, b = color
            return r > 0.8 and g > 0.8 and b < 0.2
        elif len(color) == 4:  # CMYK (0..1)
            c, m, y, k = color
            return c < 0.2 and m < 0.2 and y > 0.8 and k < 0.2
    return False

def normalize_status(high_m: float) -> str:
    """Tentukan status kondisi pasang surut BMKG"""
    if high_m >= 1.5:
        return "Bahaya"
    elif high_m >= 1.0:
        return "Waspada"
    return "Aman"

def parse_tidal_matrix(rows: List[List[Any]], filename: str = "") -> Dict[str, Any]:
    """
    Parses a 31-day x 24-hour tidal matrix where:
    - Row 0 / Header: contains column identifiers for hours (1..24)
    - Subsequent rows: First column is day of month (1..31), followed by 24 hourly water level values (in cm)
    """
    cleaned_rows = []
    
    for row in rows:
        str_cells = [str(cell).strip() if cell is not None else "" for cell in row]
        if any(str_cells):
            cleaned_rows.append(str_cells)

    if not cleaned_rows:
        return {"error": "File kosong atau tidak mengandung tabel data pasang surut."}

    # Find the header row that contains 1..24 or Jam
    header_idx = -1
    for idx, r in enumerate(cleaned_rows[:15]):
        has_hours = sum(1 for c in r if c in [str(i) for i in range(1, 25)])
        if has_hours >= 5 or "jam" in " ".join(r).lower():
            header_idx = idx
            break

    data_rows = cleaned_rows[header_idx + 1:] if header_idx != -1 else cleaned_rows

    records = []
    all_levels = []
    processed_days = {}

    for r in data_rows:
        if not r or not r[0]:
            continue
            
        first_col = r[0].strip()
        day_splits = [d.strip() for d in first_col.split('\n') if d.strip().isdigit()]
        if not day_splits:
            day_match = re.match(r"^(\d{1,2})", first_col)
            if not day_match:
                continue
            day_splits = [day_match.group(1)]

        # Collect columns 1..24
        col_splits = []
        for c in r[1:25]:
            c_str = str(c if c is not None else '').strip()
            parts = [p.strip() for p in c_str.split('\n')]
            col_splits.append(parts)

        for d_idx, day_str in enumerate(day_splits):
            day_num = int(day_str)
            if day_num < 1 or day_num > 31:
                continue

            day_records = []
            for h_idx in range(1, 25):
                col_idx = h_idx - 1
                val_num = 0
                if col_idx < len(col_splits):
                    parts = col_splits[col_idx]
                    val_str = parts[d_idx] if d_idx < len(parts) else (parts[0] if parts else '0')
                    val_str = val_str.replace(',', '').strip()
                    try:
                        val_num = int(float(val_str))
                    except (ValueError, TypeError):
                        val_num = 0

                day_records.append({
                    "day": day_num,
                    "hour": h_idx,
                    "water_level": val_num
                })

            non_zero = sum(1 for x in day_records if x["water_level"] != 0)
            if day_num not in processed_days or non_zero > processed_days[day_num]["non_zero"]:
                processed_days[day_num] = {
                    "records": day_records,
                    "non_zero": non_zero
                }

    for d in sorted(processed_days.keys()):
        for rec in processed_days[d]["records"]:
            records.append(rec)
            all_levels.append(rec["water_level"])

    if not records:
        return {"error": "Format tabel tidak sesuai format pasang surut BMKG (31 hari x 24 jam)."}

    hhw = max(all_levels) if all_levels else 0
    llw = min(all_levels) if all_levels else 0
    avg_level = round(sum(all_levels) / len(all_levels), 2) if all_levels else 0

    return {
        "status": "success",
        "file_name": os.path.basename(filename),
        "total_records": len(records),
        "days_detected": len(processed_days),
        "statistics": {
            "hhw": hhw,
            "llw": llw,
            "mean_sea_level_diff": avg_level,
            "total_hourly_points": len(records)
        },
        "data": records
    }

def parse_pdf_bmkg_enhanced(file_path: str, default_month: int = 7, default_year: int = 2026) -> Dict[str, Any]:
    """
    Ekstrak data pasang surut dari PDF BMKG dengan akurasi tinggi:
    1. Membaca matriks 31 hari x 24 jam lengkap (termasuk baris gabungan multiline).
    2. Mendeteksi sel bertanda KUNING (pasang & surut ekstrem) pada setiap tanggal.
    3. Mengekstrak bulan & tahun otomatis dari judul berkas/kop.
    """
    import pdfplumber

    with pdfplumber.open(file_path) as pdf:
        page = pdf.pages[0]
        text = page.extract_text() or ''

        # Deteksi bulan & tahun dari teks
        det_m = default_month
        det_y = default_year
        m_match = re.search(r'BULAN\s*:\s*([A-Za-z]+)\s*(\d{4})?', text, re.IGNORECASE)
        if m_match:
            m_str = m_match.group(1).lower()
            if m_str in MONTH_MAP:
                det_m = MONTH_MAP[m_str]
            if m_match.group(2):
                det_y = int(m_match.group(2))
        if not det_y:
            y_match = re.search(r'20\d{2}', text)
            if y_match:
                det_y = int(y_match.group(0))

        tables = page.find_tables()
        if not tables:
            return {"error": "Tabel pasang surut tidak terdeteksi dalam file PDF."}

        main_table = tables[0]
        extracted_data = main_table.extract()

        # Ekstrak data 24 jam per hari
        clean_days = {}
        for r in extracted_data[1:]:
            if not r or not r[0]:
                continue
            first_col = str(r[0]).strip()
            day_splits = [d.strip() for d in first_col.split('\n') if d.strip().isdigit()]
            if not day_splits:
                continue

            col_splits = []
            for c in r[1:25]:
                c_str = str(c if c is not None else '').strip()
                parts = [p.strip() for p in c_str.split('\n')]
                col_splits.append(parts)

            for d_idx, day_num_str in enumerate(day_splits):
                day_num = int(day_num_str)
                vals = []
                for col_idx in range(24):
                    parts = col_splits[col_idx]
                    val_str = parts[d_idx] if d_idx < len(parts) else (parts[0] if parts else '0')
                    val_str = val_str.replace(',', '').strip()
                    try:
                        vals.append(int(float(val_str)))
                    except (ValueError, TypeError):
                        vals.append(0)
                clean_days[day_num] = vals

        if not clean_days:
            return {"error": "Gagal membaca baris tanggal dari tabel PDF."}

        # Ekstrak sel berlatar belakang KUNING (highlight pasang surut)
        words = page.extract_words()
        yellow_rects = [r for r in page.rects if is_yellow_color(r.get('non_stroking_color'))]

        hour_words = [w for w in words if w['text'].isdigit() and 1 <= int(w['text']) <= 24 and w['top'] < 105]
        hour_xs = {}
        for w in sorted(hour_words, key=lambda w: int(w['text'])):
            h = int(w['text'])
            if h not in hour_xs:
                hour_xs[h] = (w['x0'] + w['x1']) / 2

        day_words = [w for w in words if w['text'].isdigit() and 1 <= int(w['text']) <= 31 and w['x1'] < 40 and w['top'] > 95]
        day_ys = {}
        for w in sorted(day_words, key=lambda w: w['top']):
            d = int(w['text'])
            if d not in day_ys:
                day_ys[d] = (w['top'] + w['bottom']) / 2

        day_highlights = {d: [] for d in clean_days}
        for yr in yellow_rects:
            for w in words:
                wx = (w['x0'] + w['x1']) / 2
                wy = (w['top'] + w['bottom']) / 2
                if yr['x0'] <= wx <= yr['x1'] and yr['top'] <= wy <= yr['bottom']:
                    if day_ys and hour_xs:
                        closest_d = min(day_ys.keys(), key=lambda d: abs(day_ys[d] - wy))
                        closest_h = min(hour_xs.keys(), key=lambda h: abs(hour_xs[h] - wx))
                        try:
                            val = int(w['text'])
                        except (ValueError, TypeError):
                            val = 0
                        day_highlights.setdefault(closest_d, []).append((closest_h, val))

        predictions = []
        hourly_data = []

        for d in sorted(clean_days.keys()):
            vals = clean_days[d]
            for h_idx, lvl in enumerate(vals, start=1):
                hourly_data.append({
                    'day': d,
                    'hour': h_idx,
                    'water_level': lvl
                })

            hl = day_highlights.get(d, [])
            # Tentukan Pasang dan Surut dari highlight kuning (atau fallback ke ekstremum baris)
            if hl:
                pasangs = [x for x in hl if x[1] > 0]
                suruts = [x for x in hl if x[1] <= 0]
                max_hl = max(pasangs, key=lambda x: x[1]) if pasangs else max(hl, key=lambda x: x[1])
                min_hl = min(suruts, key=lambda x: x[1]) if suruts else min(hl, key=lambda x: x[1])
                high_h, high_val_cm = max_hl
                low_h, low_val_cm = min_hl
            else:
                high_val_cm = max(vals)
                high_h = vals.index(high_val_cm) + 1
                low_val_cm = min(vals)
                low_h = vals.index(low_val_cm) + 1

            high_m = round(high_val_cm / 100.0, 2)
            low_m = round(low_val_cm / 100.0, 2)
            status = normalize_status(high_m)

            predictions.append({
                'record_date': f'{det_y:04d}-{det_m:02d}-{d:02d}',
                'day': d,
                'high_tide_time': f'{high_h:02d}.00 WIB',
                'high_tide_level': high_m,
                'low_tide_time': f'{low_h:02d}.00 WIB',
                'low_tide_level': low_m,
                'status': status
            })

        return {
            "status": "success",
            "format_type": "pdf_bmkg_highlight",
            "file_name": os.path.basename(file_path),
            "period_month": det_m,
            "period_year": det_y,
            "total_records": len(predictions),
            "predictions": predictions,
            "hourly_data": hourly_data
        }

def parse_file(file_path: str, default_month: int = 7, default_year: int = 2026) -> Dict[str, Any]:
    """Parse Excel, CSV, or PDF file into structured tidal data"""
    ext = os.path.splitext(file_path)[1].lower()

    if ext == '.pdf':
        try:
            return parse_pdf_bmkg_enhanced(file_path, default_month, default_year)
        except Exception as e:
            return {"error": f"Gagal membaca file PDF: {str(e)}"}

    elif ext in ['.xlsx', '.xls']:
        try:
            df = pd.read_excel(file_path, header=None)
            rows = df.values.tolist()
            return parse_tidal_matrix(rows, file_path)
        except Exception as e:
            return {"error": f"Gagal membaca file Excel: {str(e)}"}

    elif ext == '.csv':
        try:
            rows = []
            with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                reader = csv.reader(f)
                for r in reader:
                    rows.append(r)
            return parse_tidal_matrix(rows, file_path)
        except Exception as e:
            return {"error": f"Gagal membaca file CSV: {str(e)}"}

    return {"error": f"Format file {ext} tidak didukung. Gunakan .xlsx, .csv, atau .pdf."}
