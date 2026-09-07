# 🌊 SIPASUT — Sistem Informasi Pasang Surut Air Laut

Sistem web untuk menampilkan dan mengelola data pasang surut (tidal data) di 5 titik pantai Jawa Timur, dengan dua role: **Admin** (input data) dan **User** (visualisasi interaktif).

---

## 📂 Memahami Data PDF (Sudah Dianalisis ✅)

PDF sudah berhasil diekstrak. Sumbernya adalah **Stasiun Meteorologi Maritim Perak Surabaya (BMKG)**. Ada 5 titik pengamatan:

| Kode File | Nama Titik | Institusi |
|-----------|------------|-----------|
| `SBYTIM` | Surabaya Timur | BMKG Perak Surabaya |
| `SBYBRT` | Surabaya Barat | BMKG Perak Surabaya |
| `SBYPLB` | Surabaya Pelabuhan | BMKG Perak Surabaya |
| `KAL` | Kalianget | BMKG Perak Surabaya |
| `BWI` | Banyuwangi | BMKG Perak Surabaya |

### 📊 Struktur Data Aktual

Format data adalah **matriks Tanggal × Jam** dengan nilai tinggi air dalam **centimeter (cm)**:

```
          Jam 1  Jam 2  Jam 3  ...  Jam 24
Tgl 1  :   30     30     20   ...    10
Tgl 2  :   20     30     20   ...     0
Tgl 31 :   40     30     10   ...    ???
```

> [!NOTE]
> **Nilai bisa negatif!** Ini ketinggian relatif terhadap MSL (Mean Sea Level).
> Nilai positif = air di atas MSL (pasang). Nilai negatif = air di bawah MSL (surut).
> Range data Juli 2026 SBYTIM: **-170 cm** (surut terdalam) hingga **+140 cm** (pasang tertinggi)

---

## ⚙️ Pemilihan Framework

### 🏆 Keputusan: **Laravel (sudah ada) + Python FastAPI Microservice**

Project sudah berbasis Laravel 13. Tidak perlu migrasi ke Django.

```
Browser
  │
  ▼
Laravel (Port 8000)  ←→  SQLite / MySQL
  │  - Auth (Admin/User + Role)
  │  - Upload file (Excel/CSV/PDF)
  │  - Semua halaman Blade + Tailwind CSS
  │  - REST API internal
  │
  ▼ HTTP (Guzzle) / CLI
Python FastAPI (Port 8001)
     - Parse Excel/CSV/PDF → JSON
     - Hitung HHW, LLW, statistik
     - Return data terstruktur ke Laravel
```

---

## 🚀 Panduan Setup Awal (Langkah Demi Langkah)

Panduan ini wajib diikuti oleh setiap anggota tim saat pertama kali menjalankan project di komputer lokal.

### 📋 1. Prasyarat Sistem (Prerequisites)
Pastikan perangkat Anda sudah terpasang:
- **PHP** >= 8.3 (dengan ekstensi `pdo_sqlite`, `pdo_mysql`, `curl`, `mbstring`)
- **Composer** >= 2.x
- **Node.js** >= 18.x & **NPM**
- **Python** >= 3.9 & **Pip**

---

### 🛠️ 2. Langkah Instalasi Awal

Jalankan perintah-perintah berikut secara berurutan di terminal (PowerShell / Command Prompt):

#### **Langkah 1 — Clone Repository & Masuk ke Folder**
```bash
git clone <URL_REPOSITORY>
cd BMKG-MAGANG
```

#### **Langkah 2 — Install PHP Dependencies**
```bash
composer install
```

#### **Langkah 3 — Salin File Environment & Generate Key**
```bash
# Salin .env.example menjadi .env
copy .env.example .env     # (Windows PowerShell: Copy-Item .env.example .env)

# Generate Application Encryption Key
php artisan key:generate
```

#### **Langkah 4 — Setup Database & Seeding Data Awal**
Secara default, project menggunakan SQLite yang ringan dan siap pakai tanpa perlu konfigurasi MySQL server terpisah.
```bash
# Jalankan migrasi tabel dan seeding data (5 lokasi + akun admin & user + sample pasang surut)
php artisan migrate:fresh --seed
```

#### **Langkah 5 — Install Frontend Dependencies & Build Asset**
```bash
# Install paket Tailwind CSS, ApexCharts, dan Leaflet
npm install

# Build asset frontend
npm run build
```

#### **Langkah 6 — Setup Python Data Processing Service**
Buka terminal baru atau masuk ke folder `python_service`:
```bash
cd python_service

# Install dependencies parser Python
pip install fastapi uvicorn pandas openpyxl python-multipart pypdf

# Kembali ke folder root
cd ..
```

---

### 💻 3. Cara Menjalankan Aplikasi (Local Development)

Untuk menjalankan seluruh sistem secara lengkap, buka **2 terminal** (atau 3 terminal jika sedang mengubah CSS/JS):

#### **Terminal 1 — Server Laravel (Utama)**
```bash
php artisan serve
```
> 🌐 Akses web: **`http://127.0.0.1:8000`**

#### **Terminal 2 — Python FastAPI Microservice (Parser Data)**
```bash
cd python_service
python -m uvicorn main:app --port 8001 --reload
```
> ⚡ Microservice berjalan di: **`http://127.0.0.1:8001`**  
> 📖 Dokumentasi API Swagger: **`http://127.0.0.1:8001/docs`**

#### **Terminal 3 (Opsional) — Vite Hot Reload (saat mengedit Blade / Tailwind)**
```bash
npm run dev
```

---

### 🔑 4. Akun Login Default (Hasil Seeder)

| Role | Email | Password | Akses Halaman |
|---|---|---|---|
| **Admin BMKG** | `admin@sipasut.id` | `admin123` | Dashboard Admin (`/admin/data`), Kelola Lokasi, Kelola Notif, dll. |
| **Masyarakat / User** | `user@sipasut.id` | `user123` | Dashboard Publik (`/dashboard`), Grafik Pasang Surut, Peta, Kalender. |

*Atau buat akun baru melalui halaman registrasi di **`/register`**.*

---

### 👥 5. Catatan Pengerjaan Tampilan (Bagi Rekan Tim)
File view berikut dimasukkan ke `.gitignore` agar rekan tim dapat mendesain tampilan masing-masing tanpa tabrakan git:
- **Admin**: `resources/views/admin/kelola-lokasi.blade.php`, `kelola-profil.blade.php`, `kelola-notif.blade.php`, `login-aktivitas.blade.php`
- **User**: `resources/views/user/kondisi-pasang-surut.blade.php`, `lokasi-monitoring.blade.php`, `kalender.blade.php`
- Base Layout yang dapat di-*extend*: `@extends('layouts.app')` untuk User dan `@extends('layouts.admin')` untuk Admin.

---

## 🗃️ Struktur Database (MySQL) — Disempurnakan

```sql
-- Users & Auth
users
  id, name, email, password, role ENUM('admin','user'),
  avatar, created_at, updated_at

-- Activity Log (untuk Login Aktivitas - ADMIN)
activity_logs
  id, user_id, action, ip_address, user_agent,
  description, created_at

-- Lokasi Pantai (untuk Kelola Lokasi - ADMIN)
locations
  id, name, code, latitude, longitude,
  description, is_active, created_at, updated_at

-- Upload Data (untuk Kelola Data - ADMIN)
data_uploads
  id, location_id, uploaded_by (user_id),
  file_name, file_path, file_type ENUM('xlsx','csv'),
  period_month, period_year,
  status ENUM('pending','processing','done','error'),
  error_message, created_at, updated_at

-- Data Pasang Surut (hasil parse Python)
tidal_data
  id, upload_id, location_id,
  record_date DATE,           -- tanggal
  hour TINYINT,               -- 0-23
  water_level SMALLINT,       -- cm, bisa negatif
  created_at

-- Notifikasi (untuk Kelola Notif - ADMIN)
notifications
  id, title, message, type ENUM('info','warning','alert'),
  location_id (nullable), is_active,
  threshold_value (cm, untuk trigger alert),
  created_at, updated_at
```

---

## 🖥️ Halaman Website (dari Figma ✅)

### 👤 AUTH (Shared)
| # | Halaman | Deskripsi |
|---|---------|-----------|
| 1 | **Login / Sign In** | Form login, redirect ke dashboard sesuai role |

---

### 🔴 ADMIN — 5 Modul

#### 1. Kelola Data
- Tabel daftar semua upload data per lokasi & periode
- Upload baru: pilih lokasi, pilih bulan/tahun, upload file Excel/CSV
- Detail upload: preview data hasil parsing (tabel 31×24)
- Delete / re-upload jika ada error
- Status processing: pending → processing → done/error

#### 2. Kelola Lokasi
- Tabel 5 titik pantai (nama, koordinat, status aktif)
- CRUD lokasi: tambah, edit, hapus, toggle aktif/nonaktif
- Preview posisi di mini-map

#### 3. Kelola Profil
- Edit nama, email, password admin
- Upload avatar/foto profil
- Tampilan profil dengan info akun

#### 4. Kelola Notif
- Buat notifikasi / alert untuk user
- Setting threshold: misal "alert jika air > 130cm" atau "< -150cm"
- Toggle aktif/nonaktif notifikasi
- Daftar notifikasi yang sudah dibuat

#### 5. Login Aktivitas
- Log semua aktivitas login pengguna (admin maupun user)
- Tabel: waktu, user, IP address, aksi (login/logout/failed)
- Filter berdasarkan tanggal & user

---

### 🔵 USER — 4 Modul

#### 1. Dashboard
- Ringkasan kondisi terkini semua 5 lokasi
- Card per lokasi: nama, nilai air terkini, status (pasang/surut)
- Grafik mini per lokasi
- Notifikasi/alert terbaru dari admin

#### 2. Kondisi Pasang Surut
- Filter: pilih lokasi + bulan + tahun
- **Grafik utama**: line chart tinggi air per jam dalam satu hari (interaktif)
- **Grafik bulanan**: overview naik-turun selama 1 bulan
- Info HHW (High High Water) dan LLW (Low Low Water)
- Tooltip saat hover: tanggal, jam, ketinggian cm

#### 3. Lokasi Monitoring
- **Peta interaktif** (Leaflet.js) wilayah Jawa Timur
- 5 marker titik pengamatan, klik untuk info
- Panel samping: daftar lokasi + status terkini
- Klik marker → popup info singkat kondisi pasang surut

#### 4. Kalender
- Tampilan kalender bulanan
- Setiap tanggal: badge/indikator kondisi (pasang/surut/normal)
- Klik tanggal → popup grafik detail hari itu (per jam)
- Filter lokasi di bagian atas

---

## 🗺️ Koordinat 5 Titik Pengamatan

| Lokasi | Latitude | Longitude |
|--------|----------|-----------|
| Surabaya Timur | -7.2458 | 112.7378 |
| Surabaya Barat (Tanjung Perak) | -7.1929 | 112.7186 |
| Surabaya Pelabuhan | -7.2059 | 112.7343 |
| Kalianget, Sumenep | -7.0520 | 113.9490 |
| Banyuwangi | -8.2191 | 114.3691 |

---

## 📋 Rencana Pengembangan — 4 Fase

### Fase 1 — Setup & Fondasi (Minggu 1) — SELESAI ✅
- [x] Laravel project sudah ada & terkonfigurasi (PHP 8.3 + Laravel 13)
- [x] Auth & Role Middleware terintegrasi (Admin & User role via Spatie Permission)
- [x] Setup SQLite/MySQL database & jalankan migrasi semua tabel (`locations`, `data_uploads`, `tidal_data`, `notifications`, `activity_logs`)
- [x] Seed data: 5 lokasi titik pantau BMKG Jatim, 1 admin default (`admin@sipasut.id`), 1 user default (`user@sipasut.id`), sample data pasang surut
- [x] Setup Python FastAPI service (`python_service/main.py` + `parser.py`) dengan dependencies lengkap (`fastapi`, `uvicorn`, `pandas`, `openpyxl`, `python-multipart`, `pypdf`)
- [x] Setup Vite + Tailwind CSS v4 + ApexCharts + Leaflet.js (npm dependencies terpasang & build sukses)
- [x] Unit/Feature test passing 100%

### Fase 2 — Modul Admin (Minggu 2)
- [ ] **Auth**: Login, redirect by role, logout
- [ ] **Kelola Data**: Upload file, trigger Python parser, simpan ke DB
- [ ] **Kelola Lokasi**: CRUD tabel lokasi
- [ ] **Kelola Profil**: Edit profil admin
- [ ] **Kelola Notif**: CRUD notifikasi & threshold alert
- [ ] **Login Aktivitas**: Log otomatis setiap login/logout

### Fase 3 — Modul User (Minggu 3)
- [ ] **Dashboard**: Card 5 lokasi + grafik mini
- [ ] **Kondisi Pasang Surut**: Grafik line chart interaktif (Chart.js/ApexCharts)
- [ ] **Lokasi Monitoring**: Peta Leaflet.js + marker + popup
- [ ] **Kalender**: Tampilan kalender dengan indikator kondisi harian

### Fase 4 — Polish & Finalisasi (Minggu 4)
- [ ] Responsive design (mobile-friendly)
- [ ] Notifikasi/alert real-time untuk user
- [ ] Error handling & validasi upload
- [ ] Testing end-to-end
- [ ] Deployment / demo lokal

---

## ❓ Pertanyaan Teknis yang Masih Perlu Dijawab

> [!IMPORTANT]
> **Q1 — Format Excel Upload:**
> Admin akan upload file Excel dengan format matriks **persis seperti PDF** (baris = tanggal, kolom = jam)?
> Atau ada format lain yang sudah disiapkan tim BMKG?
> → Ini menentukan logika Python parser-nya

> [!IMPORTANT]
> **Q2 — Kalender: Data dari Lokasi Mana?**
> Halaman Kalender USER menampilkan data dari lokasi mana secara default?
> - Semua lokasi digabung?
> - User bisa pilih lokasi di kalender?

> [!IMPORTANT]
> **Q3 — Notifikasi: Push atau Hanya Tampil di Halaman?**
> "Kelola Notif" dari admin → apakah notif ini tampil sebagai:
> - **Banner/alert di dashboard user** (cukup simpel)
> - **Push notification browser** (lebih kompleks)

> [!NOTE]
> **Q4 — Deployment:**
> Lokal (localhost) atau online (VPS/hosting)?
> Ini mempengaruhi cara setup Python microservice.

> [!NOTE]
> **Q5 — Siapa handle Python FastAPI?**
> Apakah ada anggota tim yang akan mengerjakan bagian Python, atau semuanya kamu sendiri?

---

## 🔧 Tech Stack Final

| Layer | Teknologi |
|-------|-----------|
| **Frontend** | Blade + Tailwind CSS v4 |
| **Backend** | Laravel 13 (PHP 8.3) |
| **Data Processing** | Python FastAPI + pandas + openpyxl |
| **Database** | MySQL |
| **Peta** | Leaflet.js |
| **Grafik** | ApexCharts (lebih smooth untuk time-series) |
| **Kalender** | FullCalendar.js |
| **Auth** | Laravel Breeze + Role Middleware |
| **File Storage** | Laravel Storage (local disk) |
| **Laravel→Python** | Guzzle HTTP Client |
| **Activity Log** | spatie/laravel-activitylog |
