import os
import re
import csv
import pandas as pd
from typing import List, Dict, Any, Optional

def parse_tidal_matrix(rows: List[List[Any]], filename: str = "") -> Dict[str, Any]:
    """
    Parses a 31-day x 24-hour tidal matrix where:
    - Row 0 / Header: contains column identifiers for hours (1..24)
    - Subsequent rows: First column is day of month (1..31), followed by 24 hourly water level values (in cm)
    """
    cleaned_rows = []
    
    for row in rows:
        # filter out None or trailing empty strings
        str_cells = [str(cell).strip() if cell is not None else "" for cell in row]
        # skip completely empty rows
        if any(str_cells):
            cleaned_rows.append(str_cells)

    if not cleaned_rows:
        return {"error": "File kosong atau tidak mengandung tabel data pasang surut."}

    # Find the header row that contains 1..24 or Jam
    header_idx = -1
    for idx, r in enumerate(cleaned_rows[:10]):
        # check if this row has sequential hour indicators
        has_hours = sum(1 for c in r if c in [str(i) for i in range(1, 25)])
        if has_hours >= 5 or "jam" in " ".join(r).lower():
            header_idx = idx
            break

    data_rows = cleaned_rows[header_idx + 1:] if header_idx != -1 else cleaned_rows

    records = []
    all_levels = []

    for r in data_rows:
        # First column must be day number (1..31)
        if not r or not r[0]:
            continue
            
        first_col = r[0].strip()
        # Clean non-digit characters if any
        day_match = re.match(r"^(\d{1,2})", first_col)
        if not day_match:
            continue
            
        day_num = int(day_match.group(1))
        if day_num < 1 or day_num > 31:
            continue

        # Extract values for hours 1 to 24
        # Note: In PDF/table extraction, sometimes the first col contains day, next 24 columns are levels
        raw_values = r[1:25]
        
        # If columns were grouped in a single string, split by whitespace
        if len(raw_values) < 24 and len(r) == 2:
            raw_values = [v for v in r[1].split() if v.replace('-', '').isdigit()]

        hourly_data = {}
        for h_idx in range(1, 25):
            val_idx = h_idx - 1
            val_num = 0
            if val_idx < len(raw_values):
                val_str = raw_values[val_idx].replace(',', '').strip()
                try:
                    val_num = int(float(val_str))
                except (ValueError, TypeError):
                    val_num = 0
            
            hourly_data[h_idx] = val_num
            all_levels.append(val_num)
            
            records.append({
                "day": day_num,
                "hour": h_idx,
                "water_level": val_num
            })

    if not records:
        return {"error": "Format tabel tidak sesuai format pasang surut BMKG (31 hari x 24 jam)."}

    hhw = max(all_levels) if all_levels else 0
    llw = min(all_levels) if all_levels else 0
    avg_level = round(sum(all_levels) / len(all_levels), 2) if all_levels else 0

    return {
        "status": "success",
        "file_name": os.path.basename(filename),
        "total_records": len(records),
        "days_detected": len(set(r["day"] for r in records)),
        "statistics": {
            "hhw": hhw, # High High Water (Pasang Maksimum)
            "llw": llw, # Low Low Water (Surut Minimum)
            "mean_sea_level_diff": avg_level,
            "total_hourly_points": len(records)
        },
        "data": records
    }


def parse_file(file_path: str) -> Dict[str, Any]:
    """Parse Excel, CSV, or PDF file into structured tidal data"""
    ext = os.path.splitext(file_path)[1].lower()

    if ext in ['.xlsx', '.xls']:
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

    elif ext == '.pdf':
        try:
            import pdfplumber
            all_tables = []
            with pdfplumber.open(file_path) as pdf:
                for page in pdf.pages:
                    tables = page.extract_tables()
                    for t in tables:
                        all_tables.extend(t)
            
            if not all_tables:
                # Try text extraction fallback
                return {"error": "Tabel pasang surut tidak terdeteksi dalam file PDF."}
                
            return parse_tidal_matrix(all_tables, file_path)
        except Exception as e:
            return {"error": f"Gagal membaca file PDF: {str(e)}"}

    return {"error": f"Format file {ext} tidak didukung. Gunakan .xlsx, .csv, atau .pdf."}
