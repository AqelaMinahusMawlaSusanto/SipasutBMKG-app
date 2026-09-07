import os
import shutil
import tempfile
from typing import List
from fastapi import FastAPI, UploadFile, File, Form, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from parser import parse_file

app = FastAPI(
    title="SIPASUT - Tidal Data Processing Microservice",
    description="Python FastAPI backend untuk mengekstrak dan memproses data pasang surut BMKG dari Excel, CSV, dan PDF",
    version="1.0.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

class FilePathRequest(BaseModel):
    file_path: str
    location_code: str = ""
    period_month: int = 7
    period_year: int = 2026

@app.get("/")
def read_root():
    return {
        "service": "SIPASUT Python Data Processing Microservice",
        "status": "online",
        "supported_formats": [".xlsx", ".xls", ".csv", ".pdf"]
    }

@app.get("/health")
def health_check():
    return {"status": "healthy"}

@app.post("/api/parse-files")
async def parse_uploaded_files(files: List[UploadFile] = File(...)):
    """
    Menerima multi-file upload (Excel, CSV, PDF), memproses masing-masing file
    dan mengembalikan kumpulan data pasang surut terstruktur.
    """
    results = []
    temp_dir = tempfile.mkdtemp()

    try:
        for file in files:
            file_path = os.path.join(temp_dir, file.filename)
            with open(file_path, "wb") as buffer:
                shutil.copyfileobj(file.file, buffer)

            parsed_res = parse_file(file_path)
            results.append({
                "original_name": file.filename,
                "result": parsed_res
            })
    finally:
        shutil.rmtree(temp_dir, ignore_errors=True)

    return {
        "total_files": len(files),
        "results": results
    }

@app.post("/api/parse-path")
def parse_from_file_path(payload: FilePathRequest):
    """
    Memproses file yang sudah tersimpan di storage lokal Laravel via absolute path.
    """
    if not os.path.exists(payload.file_path):
        raise HTTPException(status_code=404, detail=f"File {payload.file_path} tidak ditemukan.")

    parsed_res = parse_file(payload.file_path)
    if "error" in parsed_res:
        raise HTTPException(status_code=400, detail=parsed_res["error"])

    return {
        "location_code": payload.location_code,
        "period_month": payload.period_month,
        "period_year": payload.period_year,
        "parsed": parsed_res
    }

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host="127.0.0.1", port=8001, reload=True)
