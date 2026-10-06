import sys
import json
import os
import re
import datetime
import pandas as pd
from typing import List, Dict, Any

def normalize_status(high_level: float) -> str:
    """Tentukan status kondisi berdasarkan tinggi pasang"""
    if high_level >= 2.0:
        return "Bahaya"
    elif high_level >= 1.5:
        return "Waspada"
    return "Aman"

def format_time_wib(val: Any, default_hour: int = 0) -> str:
    """Format jam menjadi 'HH.MM WIB' atau 'HH.00 WIB'"""
    if val is None or pd.isna(val) or str(val).strip() == '':
        return f"{default_hour:02d}.00 WIB"
    
    val_str = str(val).strip()
    if 'wib' in val_str.lower():
        val_str = re.sub(r'(?i)\s*wib', '', val_str).strip()
    
    # Ganti titik dua jadi titik: 03:15 -> 03.15
    val_str = val_str.replace(':', '.')
    
    # Jika hanya angka (misal "3" atau 3) -> "03.00"
    if re.match(r'^\d{1,2}$', val_str):
        h = int(val_str)
        return f"{h:02d}.00 WIB"
    
    # Jika format HH.MM
    match = re.match(r'^(\d{1,2})[\.:](\d{2})', val_str)
    if match:
        h, m = int(match.group(1)), int(match.group(2))
        return f"{h:02d}.{m:02d} WIB"
        
    return f"{val_str} WIB"

def parse_date_val(val: Any, default_month: int = 7, default_year: int = 2026) -> str:
    """Normalisasi tanggal ke format YYYY-MM-DD"""
    if val is None or pd.isna(val):
        return f"{default_year:04d}-{default_month:02d}-01"
    
    val_str = str(val).strip()
    
    # Jika integer day (1..31)
    if re.match(r'^\d{1,2}$', val_str):
        day = int(val_str)
        return f"{default_year:04d}-{default_month:02d}-{day:02d}"
        
    # Coba parse dengan pandas / datetime
    try:
        dt = pd.to_datetime(val_str, errors='coerce')
        if not pd.isna(dt):
            return dt.strftime('%Y-%m-%d')
    except Exception:
        pass

    return f"{default_year:04d}-{default_month:02d}-01"

def parse_prediction_dataframe(df: pd.DataFrame, default_month: int = 7, default_year: int = 2026) -> List[Dict[str, Any]]:
    """Parse dataframe yang memiliki kolom tabel prediksi"""
    # Standarisasi nama kolom ke lowercase tanpa spasi berlebih
    col_map = {}
    for c in df.columns:
        norm = str(c).strip().lower().replace(' ', '_').replace('-', '_')
        col_map[norm] = c

    # Cari kolom tanggal / date
    date_col = next((col_map[k] for k in ['tanggal', 'date', 'tgl', 'waktu', 'datetime', 'record_date'] if k in col_map), None)
    high_time_col = next((col_map[k] for k in ['jam_pasang', 'waktu_pasang', 'high_tide_time', 'high_time', 'jam_pasang_tertinggi'] if k in col_map), None)
    high_lvl_col = next((col_map[k] for k in ['tinggi_pasang', 'pasang_m', 'high_tide_level', 'high_level', 'tinggi_pasang_meter', 'pasang'] if k in col_map), None)
    low_time_col = next((col_map[k] for k in ['jam_surut', 'waktu_surut', 'low_tide_time', 'low_time', 'jam_surut_terendah'] if k in col_map), None)
    low_lvl_col = next((col_map[k] for k in ['tinggi_surut', 'surut_m', 'low_tide_level', 'low_level', 'tinggi_surut_meter', 'surut'] if k in col_map), None)
    status_col = next((col_map[k] for k in ['status', 'kondisi', 'keterangan'] if k in col_map), None)

    # Minimal harus ada kolom tanggal dan salah satu tinggi pasang/surut
    if not (date_col and (high_lvl_col or high_time_col)):
        return []

    predictions = []
    for _, row in df.iterrows():
        raw_date = row.get(date_col)
        if pd.isna(raw_date) or str(raw_date).strip() == '':
            continue

        date_str = parse_date_val(raw_date, default_month, default_year)

        # Parse high tide
        try:
            high_lvl = float(row.get(high_lvl_col, 0.8)) if high_lvl_col else 0.8
        except (ValueError, TypeError):
            high_lvl = 0.8

        high_time = format_time_wib(row.get(high_time_col) if high_time_col else None, default_hour=3)

        # Parse low tide
        try:
            low_lvl = float(row.get(low_lvl_col, -0.5)) if low_lvl_col else -0.5
        except (ValueError, TypeError):
            low_lvl = -0.5

        low_time = format_time_wib(row.get(low_time_col) if low_time_col else None, default_hour=12)

        # Parse status
        raw_status = str(row.get(status_col, '')).strip() if status_col else ''
        if raw_status in ['Aman', 'Waspada', 'Bahaya']:
            status = raw_status
        else:
            status = normalize_status(high_lvl)

        predictions.append({
            "record_date": date_str,
            "high_tide_time": high_time,
            "high_tide_level": round(high_lvl, 2),
            "low_tide_time": low_time,
            "low_tide_level": round(low_lvl, 2),
            "status": status
        })

    return predictions

def parse_file_comprehensive(file_path: str, month: int = 7, year: int = 2026) -> Dict[str, Any]:
    """Parse file CSV, Excel (.xlsx/.xls), atau PDF menjadi data terstruktur"""
    if not os.path.exists(file_path):
        return {"error": f"File {file_path} tidak ditemukan.", "predictions": [], "hourly_data": []}

    ext = os.path.splitext(file_path)[1].lower()
    filename = os.path.basename(file_path)

    # 1. Parsing jika file PDF
    if ext == '.pdf':
        try:
            # Import parser matrix
            from tidal_parser import parse_file as parse_pdf_matrix
            res = parse_pdf_matrix(file_path)
            if "error" in res or not res.get("data"):
                return {"error": res.get("error", "Gagal mengekstrak tabel dari PDF"), "predictions": [], "hourly_data": []}

            hourly_data = res.get("data", [])
            
            # Kelompokkan data per hari untuk mengekstrak prediksi harian (High/Low Tide)
            by_day = {}
            for item in hourly_data:
                d = item["day"]
                by_day.setdefault(d, []).append(item)

            predictions = []
            for d, hours in sorted(by_day.items()):
                max_item = max(hours, key=lambda x: x["water_level"])
                min_item = min(hours, key=lambda x: x["water_level"])

                # Skala level air: jika > 10 cm, konversi ke meter (/ 100)
                max_val = max_item["water_level"]
                min_val = min_item["water_level"]

                high_m = round(max_val / 100.0, 2) if abs(max_val) >= 10 else round(float(max_val), 2)
                low_m = round(min_val / 100.0, 2) if abs(min_val) >= 10 else round(float(min_val), 2)

                date_str = f"{year:04d}-{month:02d}-{d:02d}"
                high_time = f"{max_item['hour']:02d}.00 WIB"
                low_time = f"{min_item['hour']:02d}.00 WIB"
                status = normalize_status(high_m)

                predictions.append({
                    "record_date": date_str,
                    "day": d,
                    "high_tide_time": high_time,
                    "high_tide_level": high_m,
                    "low_tide_time": low_time,
                    "low_tide_level": low_m,
                    "status": status
                })

            return {
                "status": "success",
                "format_type": "pdf_matrix",
                "file_name": filename,
                "total_records": len(predictions),
                "predictions": predictions,
                "hourly_data": hourly_data
            }
        except Exception as e:
            return {"error": f"Gagal membaca file PDF: {str(e)}", "predictions": [], "hourly_data": []}

    # 2. Parsing jika file CSV atau Excel
    try:
        if ext == '.csv':
            try:
                df = pd.read_csv(file_path)
            except Exception:
                df = pd.read_csv(file_path, sep=';')
        else:
            df = pd.read_excel(file_path)

        # Coba parse sebagai tabel prediksi langsung
        direct_predictions = parse_prediction_dataframe(df, month, year)
        if direct_predictions:
            return {
                "status": "success",
                "format_type": "prediction_table",
                "file_name": filename,
                "total_records": len(direct_predictions),
                "predictions": direct_predictions,
                "hourly_data": []
            }

        # Jika bukan tabel prediksi standar, coba baca sebagai matrix pasang surut (31x24)
        from tidal_parser import parse_tidal_matrix
        if ext == '.csv':
            import csv
            rows = []
            with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                reader = csv.reader(f)
                for r in reader:
                    rows.append(r)
            matrix_res = parse_tidal_matrix(rows, file_path)
        else:
            matrix_df = pd.read_excel(file_path, header=None)
            matrix_res = parse_tidal_matrix(matrix_df.values.tolist(), file_path)

        if "data" in matrix_res and matrix_res["data"]:
            hourly_data = matrix_res["data"]
            by_day = {}
            for item in hourly_data:
                by_day.setdefault(item["day"], []).append(item)

            predictions = []
            for d, hours in sorted(by_day.items()):
                max_item = max(hours, key=lambda x: x["water_level"])
                min_item = min(hours, key=lambda x: x["water_level"])

                max_val = max_item["water_level"]
                min_val = min_item["water_level"]
                high_m = round(max_val / 100.0, 2) if abs(max_val) >= 10 else round(float(max_val), 2)
                low_m = round(min_val / 100.0, 2) if abs(min_val) >= 10 else round(float(min_val), 2)

                date_str = f"{year:04d}-{month:02d}-{d:02d}"
                high_time = f"{max_item['hour']:02d}.00 WIB"
                low_time = f"{min_item['hour']:02d}.00 WIB"
                status = normalize_status(high_m)

                predictions.append({
                    "record_date": date_str,
                    "day": d,
                    "high_tide_time": high_time,
                    "high_tide_level": high_m,
                    "low_tide_time": low_time,
                    "low_tide_level": low_m,
                    "status": status
                })

            return {
                "status": "success",
                "format_type": "matrix",
                "file_name": filename,
                "total_records": len(predictions),
                "predictions": predictions,
                "hourly_data": hourly_data
            }

        return {
            "error": "Format tabel file tidak dikenali. Pastikan file memiliki format matriks BMKG atau kolom: tanggal, jam_pasang, tinggi_pasang, jam_surut, tinggi_surut, status.",
            "predictions": [],
            "hourly_data": []
        }

    except Exception as e:
        return {"error": f"Gagal membaca file: {str(e)}", "predictions": [], "hourly_data": []}

if __name__ == "__main__":
    if len(sys.argv) > 1:
        file_path = sys.argv[1]
        month = int(sys.argv[2]) if len(sys.argv) > 2 and sys.argv[2].isdigit() else 7
        year = int(sys.argv[3]) if len(sys.argv) > 3 and sys.argv[3].isdigit() else 2026

        res = parse_file_comprehensive(file_path, month, year)
        print(json.dumps(res))
    else:
        print(json.dumps({"error": "Tidak ada argumen file_path", "predictions": [], "hourly_data": []}))