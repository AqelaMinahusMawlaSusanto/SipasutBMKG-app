import sys
import json
import os
import pandas as pd
import pdfplumber

def parse_excel_or_csv(file_path):
    records = []
    if file_path.endswith('.csv'):
        df = pd.read_csv(file_path)
    else:
        df = pd.read_excel(file_path)
    
    # Standarisasi nama kolom (case insensitive)
    df.columns = [str(c).strip().lower() for c in df.columns]
    
    for _, row in df.iterrows():
        datetime_val = str(row.get('waktu', row.get('datetime', row.get('tanggal', ''))))
        water_level = row.get('ketinggian_cm', row.get('tinggi', row.get('water_level', 0)))
        status_val = str(row.get('status', 'Normal'))
        
        try:
            water_level = float(water_level)
        except (ValueError, TypeError):
            water_level = 0.0

        if datetime_val:
            records.append({
                "datetime": datetime_val,
                "water_level": water_level,
                "status": status_val if status_val != 'nan' else 'Normal'
            })
    return records

def parse_pdf(file_path):
    records = []
    with pdfplumber.open(file_path) as pdf:
        for page in pdf.pages:
            tables = page.extract_tables()
            for table in tables:
                for row in table[1:]:  # Skip header
                    if len(row) >= 2:
                        dt = str(row[0]).strip() if row[0] else ""
                        try:
                            wl = float(row[1]) if row[1] else 0.0
                        except ValueError:
                            wl = 0.0
                        st = str(row[2]).strip() if len(row) > 2 and row[2] else "Normal"
                        
                        if dt:
                            records.append({
                                "datetime": dt,
                                "water_level": wl,
                                "status": st
                            })
    return records

if __name__ == "__main__":
    if len(sys.argv) > 1:
        file_path = sys.argv[1]
        if not os.path.exists(file_path):
            print(json.dumps([]))
            sys.exit(0)

        try:
            if file_path.endswith('.pdf'):
                data = parse_pdf(file_path)
            else:
                data = parse_excel_or_csv(file_path)
            print(json.dumps(data))
        except Exception as e:
            print(json.dumps([]))
    else:
        print(json.dumps([]))