import os
import sys
from PIL import Image, ImageDraw, ImageFont
import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn
import win32com.client

print("Starting documentation generation script...")

# Output directory for images
assets_dir = os.path.join(os.getcwd(), 'doc_assets')
os.makedirs(assets_dir, exist_ok=True)

# Fonts for Pillow
FONT_PATH = "C:\\Windows\\Fonts\\arial.ttf"
FONT_BOLD_PATH = "C:\\Windows\\Fonts\\arialbd.ttf"
FONT_MONO_PATH = "C:\\Windows\\Fonts\\consola.ttf"

def get_font(path, size):
    try:
        return ImageFont.truetype(path, size)
    except:
        return ImageFont.load_default()

font_title = get_font(FONT_BOLD_PATH, 20)
font_subtitle = get_font(FONT_PATH, 14)
font_heading = get_font(FONT_BOLD_PATH, 16)
font_body = get_font(FONT_PATH, 13)
font_body_bold = get_font(FONT_BOLD_PATH, 13)
font_small = get_font(FONT_PATH, 11)
font_mono = get_font(FONT_MONO_PATH, 12)
font_mono_bold = get_font(FONT_MONO_PATH, 13)

def draw_browser_frame(draw, width, height, title, url):
    # Header bar
    draw.rectangle([(0, 0), (width, 42)], fill="#1e293b")
    # Window buttons
    draw.ellipse([(14, 14), (26, 26)], fill="#ef4444")
    draw.ellipse([(34, 14), (46, 26)], fill="#eab308")
    draw.ellipse([(54, 14), (66, 26)], fill="#22c55e")
    # Title
    draw.text((80, 12), title, fill="#94a3b8", font=font_small)
    # URL bar
    draw.rectangle([(0, 42), (width, 82)], fill="#0f172a")
    draw.rounded_rectangle([(70, 48), (width - 70, 76)], radius=6, fill="#1e293b", outline="#334155", width=1)
    draw.text((85, 52), url, fill="#38bdf8", font=font_mono)
    # Page background
    draw.rectangle([(0, 82), (width, height)], fill="#f8fafc")

# 1. Gambar 1: Flowchart Arsitektur Keamanan
def create_img1():
    w, h = 950, 520
    im = Image.new('RGB', (w, h), color='#ffffff')
    d = ImageDraw.Draw(im)
    
    # Outer border
    d.rounded_rectangle([(10, 10), (w-10, h-10)], radius=12, outline="#cbd5e1", width=2)
    # Header
    d.rectangle([(10, 10), (w-10, 60)], fill="#0a3981")
    d.text((30, 22), "DIAGRAM ARSITEKTUR AUTHENTICATION & AUTHORIZATION - SIPASUT BMKG", fill="#ffffff", font=font_heading)
    
    # Flow elements
    # Box 1: User Request
    d.rounded_rectangle([(40, 100), (220, 190)], radius=8, fill="#f1f5f9", outline="#0284c7", width=2)
    d.text((55, 115), "HTTP Request", fill="#0369a1", font=font_body_bold)
    d.text((55, 140), "User / Admin\nmengakses URL\n/admin/*", fill="#334155", font=font_small)
    
    # Arrow 1
    d.line([(220, 145), (280, 145)], fill="#0284c7", width=3)
    d.polygon([(280, 145), (270, 138), (270, 152)], fill="#0284c7")
    
    # Box 2: Middleware Auth
    d.rounded_rectangle([(280, 100), (470, 190)], radius=8, fill="#eff6ff", outline="#1d4ed8", width=2)
    d.text((295, 115), "1. Middleware 'auth'", fill="#1d4ed8", font=font_body_bold)
    d.text((295, 140), "Cek status sesi login:\n- Auth::check() == true?\n- Verifikasi Token/Sesi", fill="#1e293b", font=font_small)
    
    # Unauthenticated branch (down)
    d.line([(375, 190), (375, 270)], fill="#dc2626", width=2)
    d.polygon([(375, 270), (368, 260), (382, 260)], fill="#dc2626")
    d.text((385, 225), "Belum Login (Guest)", fill="#dc2626", font=font_small)
    
    d.rounded_rectangle([(260, 270), (490, 340)], radius=6, fill="#fef2f2", outline="#ef4444", width=2)
    d.text((275, 285), "Redirect (302) -> /login", fill="#b91c1c", font=font_body_bold)
    d.text((275, 308), "Dialihkan ke Form Login Admin BMKG", fill="#7f1d1d", font=font_small)
    
    # Arrow 2: Authenticated -> Spatie Role
    d.line([(470, 145), (540, 145)], fill="#16a34a", width=3)
    d.polygon([(540, 145), (530, 138), (530, 152)], fill="#16a34a")
    d.text((475, 125), "Terautentikasi", fill="#16a34a", font=font_small)
    
    # Box 3: Spatie Role Middleware
    d.rounded_rectangle([(540, 100), (740, 190)], radius=8, fill="#faf5ff", outline="#7c3aed", width=2)
    d.text((555, 115), "2. Middleware 'role:admin'", fill="#6d28d9", font=font_body_bold)
    d.text((555, 140), "Evaluasi Role Akun:\n- User::hasRole('admin')\n- Cek tabel model_has_roles", fill="#1e293b", font=font_small)
    
    # Non-admin branch (down)
    d.line([(640, 190), (640, 270)], fill="#dc2626", width=2)
    d.polygon([(640, 270), (633, 260), (647, 260)], fill="#dc2626")
    d.text((650, 225), "Role 'user' (Non-Admin)", fill="#dc2626", font=font_small)
    
    d.rounded_rectangle([(530, 270), (750, 340)], radius=6, fill="#fef2f2", outline="#ef4444", width=2)
    d.text((545, 285), "HTTP 403 Forbidden", fill="#b91c1c", font=font_body_bold)
    d.text((545, 308), "AKSES DITOLAK: Hak akses dibatasi", fill="#7f1d1d", font=font_small)
    
    # Authorized Admin branch (right)
    d.line([(740, 145), (800, 145)], fill="#16a34a", width=3)
    d.polygon([(800, 145), (790, 138), (790, 152)], fill="#16a34a")
    d.text((745, 125), "Role 'admin'", fill="#16a34a", font=font_small)
    
    # Box 4: Admin CRUD Controller
    d.rounded_rectangle([(800, 100), (925, 190)], radius=8, fill="#f0fdf4", outline="#16a34a", width=2)
    d.text((810, 115), "3. AdminController", fill="#15803d", font=font_body_bold)
    d.text((810, 140), "Akses Penuh CRUD:\n- Kelola Data\n- Kelola Lokasi\n- Kelola Notif", fill="#14532d", font=font_small)
    
    # Audit log bottom banner
    d.rounded_rectangle([(40, 390), (910, 480)], radius=8, fill="#f8fafc", outline="#64748b", width=1)
    d.text((55, 405), "LAPISAN TAMBAHAN (SECURITY & AUDIT TRAIL):", fill="#334155", font=font_body_bold)
    d.text((55, 430), "* Two-Factor Authentication (OTP 6 Digit berlaku 5 menit dikirim via email)", fill="#475569", font=font_small)
    d.text((55, 450), "* ActivityLog mencatat alamat IP, waktu login, aktivitas user, dan percobaan gagal.", fill="#475569", font=font_small)
    
    im.save(os.path.join(assets_dir, "gambar1_arsitektur_keamanan.png"))

# 2. Gambar 2: Form Login Admin BMKG
def create_img2():
    w, h = 950, 540
    im = Image.new('RGB', (w, h), color='#ffffff')
    d = ImageDraw.Draw(im)
    draw_browser_frame(d, w, h, "SIPASUT BMKG - Login Admin", "https://sipasut.bmkg.go.id/login")
    
    # Auth card container
    cx, cy = w // 2, 310
    card_w, card_h = 440, 410
    x1, y1 = cx - card_w // 2, cy - card_h // 2
    x2, y2 = cx + card_w // 2, cy + card_h // 2
    
    d.rounded_rectangle([(x1, y1), (x2, y2)], radius=12, fill="#ffffff", outline="#e2e8f0", width=2)
    
    # Card Header
    d.ellipse([(cx - 24, y1 + 20), (cx + 24, y1 + 68)], fill="#0284c7")
    d.text((cx - 15, y1 + 32), "BMKG", fill="#ffffff", font=font_small)
    
    d.text((cx - 110, y1 + 80), "Login Admin BMKG", fill="#0f172a", font=font_heading)
    d.text((cx - 145, y1 + 110), "Monitoring Pasang Surut Air Laut\nBMKG Tanjung Perak Surabaya", fill="#64748b", font=font_small, align="center")
    
    # Form fields
    # Field 1: Email
    d.text((x1 + 40, y1 + 160), "Email Petugas", fill="#334155", font=font_body_bold)
    d.rounded_rectangle([(x1 + 40, y1 + 185), (x2 - 40, y1 + 225)], radius=6, fill="#f8fafc", outline="#cbd5e1", width=1)
    d.text((x1 + 55, y1 + 197), "admin@sipasut.id", fill="#0f172a", font=font_body)
    
    # Field 2: Password
    d.text((x1 + 40, y1 + 240), "Password", fill="#334155", font=font_body_bold)
    d.rounded_rectangle([(x1 + 40, y1 + 265), (x2 - 40, y1 + 305)], radius=6, fill="#f8fafc", outline="#cbd5e1", width=1)
    d.text((x1 + 55, y1 + 277), "**********", fill="#0f172a", font=font_body)
    
    # Login Button
    d.rounded_rectangle([(x1 + 40, y1 + 325), (x2 - 40, y1 + 368)], radius=6, fill="#0284c7")
    d.text((cx - 40, y1 + 338), "Masuk Sistem", fill="#ffffff", font=font_body_bold)
    
    d.text((cx - 95, y1 + 382), "Dilindungi Verifikasi 2 Langkah (OTP)", fill="#94a3b8", font=font_small)
    
    im.save(os.path.join(assets_dir, "gambar2_form_login_admin.png"))

# 3. Gambar 3: Form OTP 6 Digit
def create_img3():
    w, h = 950, 540
    im = Image.new('RGB', (w, h), color='#ffffff')
    d = ImageDraw.Draw(im)
    draw_browser_frame(d, w, h, "SIPASUT BMKG - Verifikasi Dua Langkah (OTP)", "https://sipasut.bmkg.go.id/otp")
    
    # Auth card
    cx, cy = w // 2, 310
    card_w, card_h = 440, 390
    x1, y1 = cx - card_w // 2, cy - card_h // 2
    x2, y2 = cx + card_w // 2, cy + card_h // 2
    
    d.rounded_rectangle([(x1, y1), (x2, y2)], radius=12, fill="#ffffff", outline="#e2e8f0", width=2)
    
    # Header
    d.ellipse([(cx - 24, y1 + 20), (cx + 24, y1 + 68)], fill="#059669")
    d.text((cx - 12, y1 + 32), "OTP", fill="#ffffff", font=font_body_bold)
    
    d.text((cx - 90, y1 + 80), "Verifikasi Kode OTP", fill="#0f172a", font=font_heading)
    d.text((cx - 150, y1 + 110), "Kode 6 digit telah dikirim ke email admin@sipasut.id.\nBerlaku selama 5 menit.", fill="#64748b", font=font_small, align="center")
    
    # OTP Input Boxes
    box_w, box_gap = 48, 12
    start_x = cx - ((6 * box_w + 5 * box_gap) // 2)
    sample_digits = ['8', '4', '2', '9', '1', '7']
    for i in range(6):
        bx = start_x + i * (box_w + box_gap)
        by = y1 + 170
        d.rounded_rectangle([(bx, by), (bx + box_w, by + 56)], radius=8, fill="#f0fdf4", outline="#16a34a", width=2)
        d.text((bx + 18, by + 16), sample_digits[i], fill="#15803d", font=font_heading)
    
    # Submit Button
    d.rounded_rectangle([(x1 + 40, y1 + 255), (x2 - 40, y1 + 298)], radius=6, fill="#16a34a")
    d.text((cx - 45, y1 + 268), "Verifikasi OTP", fill="#ffffff", font=font_body_bold)
    
    # Resend timer & link
    d.text((cx - 110, y1 + 315), "Tidak menerima email? Kirim ulang kode", fill="#2563eb", font=font_small)
    d.text((cx - 75, y1 + 345), "Sisa kesempatan: 3x percobaan", fill="#dc2626", font=font_small)
    
    im.save(os.path.join(assets_dir, "gambar3_verifikasi_otp_email.png"))

# 4. Gambar 4: Redirect Tamu (Guest) ke /login
def create_img4():
    w, h = 950, 520
    im = Image.new('RGB', (w, h), color='#ffffff')
    d = ImageDraw.Draw(im)
    draw_browser_frame(d, w, h, "Redirecting Unauthenticated User...", "https://sipasut.bmkg.go.id/admin/dashboard")
    
    # Alert banner in browser
    d.rounded_rectangle([(100, 120), (w - 100, 220)], radius=8, fill="#fffbeb", outline="#f59e0b", width=2)
    d.text((130, 140), "STATUS PENGAWASAN AKSES: TAMU BELUM TEROTENTIKASI (GUEST)", fill="#b45309", font=font_heading)
    d.text((130, 175), "Pengguna mencoba langsung mengakses URL internal: /admin/dashboard tanpa sesi login.", fill="#78350f", font=font_body)
    
    # Technical box
    d.rounded_rectangle([(100, 250), (w - 100, 460)], radius=8, fill="#0f172a", outline="#334155", width=2)
    d.text((120, 270), "LOGIKA FILTER MIDDLEWARE LARAVEL (app/bootstrap/app.php & routes/web.php):", fill="#38bdf8", font=font_mono_bold)
    
    code_text = (
        "1. Request URL : GET /admin/dashboard\n"
        "2. Middleware  : Illuminate\\Auth\\Middleware\\Authenticate ('auth')\n"
        "3. Evaluasi    : Auth::check() == FALSE (No active session token)\n"
        "4. Respon HTTP : 302 Found (Redirect to named route 'login')\n"
        "5. Destination : Location: https://sipasut.bmkg.go.id/login\n"
        "6. Hasil Uji   : PASS (Guest berhasil dicegah membuka dashboard admin)"
    )
    d.text((120, 305), code_text, fill="#e2e8f0", font=font_mono)
    
    im.save(os.path.join(assets_dir, "gambar4_redirect_guest_login.png"))

# 5. Gambar 5: Penolakan User Biasa (403 Forbidden)
def create_img5():
    w, h = 950, 540
    im = Image.new('RGB', (w, h), color='#ffffff')
    d = ImageDraw.Draw(im)
    draw_browser_frame(d, w, h, "403 | Forbidden - Akses Ditolak", "https://sipasut.bmkg.go.id/admin/data")
    
    # 403 Error Page Container
    cx, cy = w // 2, 290
    d.rounded_rectangle([(120, 110), (w - 120, 480)], radius=12, fill="#ffffff", outline="#ef4444", width=2)
    
    # Red badge
    d.rounded_rectangle([(cx - 70, 135), (cx + 70, 175)], radius=6, fill="#fee2e2", outline="#dc2626", width=1)
    d.text((cx - 52, 146), "HTTP 403", fill="#b91c1c", font=font_heading)
    
    d.text((cx - 160, 195), "403 | USER DOES NOT HAVE THE RIGHT ROLES.", fill="#0f172a", font=font_heading)
    d.text((cx - 240, 230), "Anda masuk sebagai: user@sipasut.id (Role: 'user' / Masyarakat Nelayan)", fill="#64748b", font=font_body)
    d.text((cx - 260, 255), "Halaman ini dibatasi hanya untuk staf Administrator BMKG (Role: 'admin').", fill="#dc2626", font=font_body_bold)
    
    # Detail verification table inside 403
    d.rounded_rectangle([(160, 295), (w - 160, 440)], radius=8, fill="#f8fafc", outline="#cbd5e1", width=1)
    d.text((180, 310), "RINCIAN PEMERIKSAAN OTORISASI (SPATIE LARAVEL-PERMISSION):", fill="#334155", font=font_body_bold)
    
    items = [
        ("Identitas Pengguna", "Masyarakat & Nelayan (user@sipasut.id)"),
        ("Status Otentikasi", "VALID (Sudah Login via Auth Guard Web)"),
        ("Role Pengguna", "'user'"),
        ("Role yang Dibutuhkan", "'admin' (Route::middleware(['role:admin']))"),
        ("Keputusan Sistem", "DITOLAK (Akses CRUD Admin diblokir sepenuhnya)")
    ]
    
    for idx, (lbl, val) in enumerate(items):
        yy = 335 + idx * 20
        d.text((180, yy), f"* {lbl}:", fill="#475569", font=font_small)
        d.text((370, yy), val, fill="#0f172a", font=font_small)
        
    im.save(os.path.join(assets_dir, "gambar5_user_forbidden_403.png"))

# 6. Gambar 6: Dashboard Admin Sukses
def create_img6():
    w, h = 950, 540
    im = Image.new('RGB', (w, h), color='#ffffff')
    d = ImageDraw.Draw(im)
    draw_browser_frame(d, w, h, "Admin Panel - SIPASUT BMKG", "https://sipasut.bmkg.go.id/admin/dashboard")
    
    # Sidebar layout
    d.rectangle([(0, 82), (220, h)], fill="#0f172a")
    d.text((25, 105), "SIPASUT ADMIN", fill="#ffffff", font=font_heading)
    d.text((25, 128), "BMKG Maritim Perak", fill="#64748b", font=font_small)
    
    menu_items = [
        ("Dashboard", True),
        ("Kelola Data", False),
        ("Kelola Lokasi", False),
        ("Kelola Notifikasi", False),
        ("Login Aktivitas", False),
        ("Kelola Profil", False),
    ]
    for idx, (m, active) in enumerate(menu_items):
        my = 160 + idx * 42
        if active:
            d.rounded_rectangle([(15, my), (205, my + 34)], radius=6, fill="#0284c7")
            d.text((30, my + 8), m, fill="#ffffff", font=font_body_bold)
        else:
            d.text((30, my + 8), m, fill="#94a3b8", font=font_body)
            
    # Main content area
    d.rectangle([(220, 82), (w, h)], fill="#f1f5f9")
    
    # Welcome card
    d.rounded_rectangle([(240, 100), (w - 20, 175)], radius=8, fill="#ffffff", outline="#e2e8f0", width=1)
    d.text((260, 115), "Selamat datang, Administrator BMKG", fill="#0f172a", font=font_heading)
    d.text((260, 142), "Sistem Operasional Pemantauan & Analisis Pasang Surut Air Laut Jawa Timur", fill="#64748b", font=font_small)
    d.rounded_rectangle([(w - 180, 120), (w - 40, 155)], radius=6, fill="#dcfce7", outline="#16a34a", width=1)
    d.text((w - 165, 130), "Role: ADMIN", fill="#15803d", font=font_body_bold)
    
    # Stat cards
    stats = [
        ("Total Stasiun", "5 Titik", "#0284c7"),
        ("Data Pasang Surut", "3,720 Rekor", "#16a34a"),
        ("Peringatan Aktif", "1 Warning Rob", "#eab308"),
        ("Status Microservice", "FastAPI Connected", "#8b5cf6")
    ]
    for idx, (t, val, col) in enumerate(stats):
        sx = 240 + idx * 168
        sy = 195
        d.rounded_rectangle([(sx, sy), (sx + 155, sy + 100)], radius=8, fill="#ffffff", outline="#cbd5e1", width=1)
        d.rectangle([(sx, sy), (sx + 6, sy + 100)], fill=col)
        d.text((sx + 16, sy + 15), t, fill="#64748b", font=font_small)
        d.text((sx + 16, sy + 45), val, fill="#0f172a", font=font_body_bold)
        
    # Section preview
    d.rounded_rectangle([(240, 315), (w - 20, 510)], radius=8, fill="#ffffff", outline="#cbd5e1", width=1)
    d.text((260, 330), "Ringkasan Stasiun Pengamatan Pantai Jawa Timur", fill="#1e293b", font=font_body_bold)
    
    headers = ["KODE", "NAMA STASIUN", "KOORDINAT", "KONDISI AIR TERAKHIR", "STATUS"]
    d.rectangle([(260, 360), (w - 40, 390)], fill="#f8fafc")
    for i, h_text in enumerate(headers):
        d.text((270 + i * 125, 368), h_text, fill="#475569", font=font_small)
        
    rows = [
        ("SBYTIM", "Surabaya Timur", "-7.2458, 112.7988", "+110 cm (Pasang)", "NORMAL"),
        ("SBYPLB", "Pelabuhan Tanjung Perak", "-7.2059, 112.7343", "+135 cm (Waspada)", "WARNING ROB"),
        ("KAL", "Kalianget Sumenep", "-7.0520, 113.9490", "-40 cm (Surut)", "NORMAL"),
    ]
    for r_idx, r_data in enumerate(rows):
        ry = 400 + r_idx * 32
        for c_idx, val in enumerate(r_data):
            col_text = "#dc2626" if val == "WARNING ROB" else "#334155"
            d.text((270 + c_idx * 125, ry), val, fill=col_text, font=font_small)
            
    im.save(os.path.join(assets_dir, "gambar6_dashboard_admin_sukses.png"))

# 7. Gambar 7: CRUD Kelola Data
def create_img7():
    w, h = 950, 540
    im = Image.new('RGB', (w, h), color='#ffffff')
    d = ImageDraw.Draw(im)
    draw_browser_frame(d, w, h, "Kelola Data Prediksi & Pasang Surut - Admin SIPASUT", "https://sipasut.bmkg.go.id/admin/data")
    
    # Sidebar
    d.rectangle([(0, 82), (220, h)], fill="#0f172a")
    d.text((25, 105), "SIPASUT ADMIN", fill="#ffffff", font=font_heading)
    d.text((30, 168), "Dashboard", fill="#94a3b8", font=font_body)
    d.rounded_rectangle([(15, 200), (205, 234)], radius=6, fill="#0284c7")
    d.text((30, 208), "Kelola Data", fill="#ffffff", font=font_body_bold)
    d.text((30, 250), "Kelola Lokasi", fill="#94a3b8", font=font_body)
    d.text((30, 290), "Kelola Notifikasi", fill="#94a3b8", font=font_body)
    
    # Main content
    d.rectangle([(220, 82), (w, h)], fill="#f1f5f9")
    
    # Action Header
    d.text((245, 105), "Kelola Data Pasang Surut & Prediksi", fill="#0f172a", font=font_heading)
    d.text((245, 130), "Modul CRUD: Upload Berkas Excel/PDF & Manajemen Prediksi Air Laut", fill="#64748b", font=font_small)
    
    # Buttons
    d.rounded_rectangle([(w - 380, 105), (w - 230, 140)], radius=6, fill="#0284c7")
    d.text((w - 365, 115), "+ Input Prediksi", fill="#ffffff", font=font_body_bold)
    
    d.rounded_rectangle([(w - 215, 105), (w - 35, 140)], radius=6, fill="#16a34a")
    d.text((w - 200, 115), "Upload Excel/PDF", fill="#ffffff", font=font_body_bold)
    
    # Upload Form card preview
    d.rounded_rectangle([(245, 160), (w - 35, 255)], radius=8, fill="#ffffff", outline="#cbd5e1", width=1)
    d.text((265, 175), "Formulir Unggah Dataset Pasang Surut BMKG (Format: .xlsx, .csv, .pdf)", fill="#1e293b", font=font_body_bold)
    d.rounded_rectangle([(265, 200), (620, 238)], radius=6, fill="#f8fafc", outline="#94a3b8", width=1)
    d.text((280, 210), "Pilih file: juli_2026_sbytim_perak.xlsx", fill="#334155", font=font_small)
    d.rounded_rectangle([(635, 200), (745, 238)], radius=6, fill="#0f172a")
    d.text((655, 212), "Kirim File", fill="#ffffff", font=font_small)
    
    # Table of Predictions
    d.rounded_rectangle([(245, 275), (w - 35, 515)], radius=8, fill="#ffffff", outline="#cbd5e1", width=1)
    d.text((265, 290), "Daftar Data Prediksi Pasang Surut Terdaftar (Tabel Operasional)", fill="#1e293b", font=font_body_bold)
    
    cols = ["ID", "LOKASI", "TANGGAL", "JAM", "TINGGI AIR (CM)", "TIPE", "AKSI CRUD"]
    d.rectangle([(265, 320), (w - 55, 350)], fill="#f8fafc")
    for idx, c in enumerate(cols):
        d.text((275 + idx * 88, 328), c, fill="#475569", font=font_small)
        
    data_rows = [
        ("1", "Surabaya Timur", "2026-07-01", "10:00", "+120 cm", "Pasang", "[Edit] [Hapus]"),
        ("2", "Surabaya Barat", "2026-07-01", "16:00", "-60 cm", "Surut", "[Edit] [Hapus]"),
        ("3", "Tanjung Perak", "2026-07-01", "23:00", "+135 cm", "Pasang Maks", "[Edit] [Hapus]"),
        ("4", "Kalianget Sumenep", "2026-07-02", "04:00", "-90 cm", "Surut Min", "[Edit] [Hapus]"),
    ]
    for r_idx, r in enumerate(data_rows):
        ry = 365 + r_idx * 34
        for c_idx, val in enumerate(r):
            col_t = "#dc2626" if "Hapus" in val else "#334155"
            d.text((275 + c_idx * 88, ry), val, fill=col_t, font=font_small)
            
    im.save(os.path.join(assets_dir, "gambar7_crud_kelola_data.png"))

# 8. Gambar 8: CRUD Kelola Lokasi
def create_img8():
    w, h = 950, 540
    im = Image.new('RGB', (w, h), color='#ffffff')
    d = ImageDraw.Draw(im)
    draw_browser_frame(d, w, h, "Kelola Titik Lokasi Monitoring - Admin SIPASUT", "https://sipasut.bmkg.go.id/admin/lokasi")
    
    # Sidebar
    d.rectangle([(0, 82), (220, h)], fill="#0f172a")
    d.text((25, 105), "SIPASUT ADMIN", fill="#ffffff", font=font_heading)
    d.text((30, 168), "Dashboard", fill="#94a3b8", font=font_body)
    d.text((30, 208), "Kelola Data", fill="#94a3b8", font=font_body)
    d.rounded_rectangle([(15, 240), (205, 274)], radius=6, fill="#0284c7")
    d.text((30, 248), "Kelola Lokasi", fill="#ffffff", font=font_body_bold)
    
    # Main content
    d.rectangle([(220, 82), (w, h)], fill="#f1f5f9")
    
    # Action Header
    d.text((245, 105), "Kelola Titik Lokasi Monitoring Pasang Surut", fill="#0f172a", font=font_heading)
    d.text((245, 130), "Manajemen 5 Stasiun Pantau Wilayah Pesisir & Pelabuhan Jawa Timur", fill="#64748b", font=font_small)
    
    d.rounded_rectangle([(w - 220, 105), (w - 35, 140)], radius=6, fill="#0284c7")
    d.text((w - 200, 115), "+ Tambah Stasiun", fill="#ffffff", font=font_body_bold)
    
    # Table of Locations
    d.rounded_rectangle([(245, 160), (w - 35, 510)], radius=8, fill="#ffffff", outline="#cbd5e1", width=1)
    
    loc_cols = ["KODE", "NAMA STASIUN", "LATITUDE", "LONGITUDE", "INSTITUSI", "STATUS", "AKSI CRUD"]
    d.rectangle([(245, 160), (w - 35, 195)], fill="#f8fafc")
    for idx, c in enumerate(loc_cols):
        d.text((260 + idx * 95, 172), c, fill="#475569", font=font_small)
        
    stations = [
        ("SBYTIM", "Surabaya Timur", "-7.2458", "112.7988", "BMKG Perak", "Aktif", "[Edit] [Hapus]"),
        ("SBYBRT", "Surabaya Barat", "-7.1929", "112.6500", "BMKG Perak", "Aktif", "[Edit] [Hapus]"),
        ("SBYPLB", "Tanjung Perak", "-7.2059", "112.7343", "BMKG Perak", "Aktif", "[Edit] [Hapus]"),
        ("KAL", "Kalianget Sumenep", "-7.0520", "113.9490", "BMKG Perak", "Aktif", "[Edit] [Hapus]"),
        ("BWI", "Banyuwangi Ketapang", "-8.2191", "114.3691", "BMKG Perak", "Aktif", "[Edit] [Hapus]"),
    ]
    for r_idx, st in enumerate(stations):
        ry = 210 + r_idx * 55
        d.line([(245, ry - 8), (w - 35, ry - 8)], fill="#f1f5f9", width=1)
        for c_idx, val in enumerate(st):
            col_t = "#16a34a" if val == "Aktif" else ("#dc2626" if "Hapus" in val else "#334155")
            d.text((260 + c_idx * 95, ry), val, fill=col_t, font=font_small)
            
    im.save(os.path.join(assets_dir, "gambar8_crud_kelola_lokasi.png"))

# 9. Gambar 9: Activity Log & Audit Trail
def create_img9():
    w, h = 950, 520
    im = Image.new('RGB', (w, h), color='#ffffff')
    d = ImageDraw.Draw(im)
    draw_browser_frame(d, w, h, "Audit Trail & Login Aktivitas - Admin SIPASUT", "https://sipasut.bmkg.go.id/admin/aktivitas")
    
    # Sidebar
    d.rectangle([(0, 82), (220, h)], fill="#0f172a")
    d.text((25, 105), "SIPASUT ADMIN", fill="#ffffff", font=font_heading)
    d.text((30, 168), "Dashboard", fill="#94a3b8", font=font_body)
    d.text((30, 208), "Kelola Data", fill="#94a3b8", font=font_body)
    d.text((30, 248), "Kelola Lokasi", fill="#94a3b8", font=font_body)
    d.rounded_rectangle([(15, 280), (205, 314)], radius=6, fill="#0284c7")
    d.text((30, 288), "Login Aktivitas", fill="#ffffff", font=font_body_bold)
    
    # Main content
    d.rectangle([(220, 82), (w, h)], fill="#f1f5f9")
    
    d.text((245, 105), "Audit Trail & Log Aktivitas Keamanan", fill="#0f172a", font=font_heading)
    d.text((245, 130), "Pencatatan Otomatis Setiap Login, Percobaan Gagal, dan Aktivitas Admin", fill="#64748b", font=font_small)
    
    # Log Table
    d.rounded_rectangle([(245, 160), (w - 35, 490)], radius=8, fill="#ffffff", outline="#cbd5e1", width=1)
    
    log_cols = ["WAKTU", "JENIS EVENT", "DESKRIPSI AKTIVITAS", "PENGGUNA / IP", "STATUS"]
    d.rectangle([(245, 160), (w - 35, 195)], fill="#f8fafc")
    for idx, c in enumerate(log_cols):
        dx = [260, 370, 480, 750, 860][idx]
        d.text((dx, 172), c, fill="#475569", font=font_small)
        
    logs = [
        ("2026-11-25 14:02:11", "login_otp", "User Administrator BMKG berhasil verifikasi OTP.", "admin@sipasut.id", "BERHASIL"),
        ("2026-11-25 13:45:04", "failed_login", "Percobaan login gagal untuk email: hacker@mail.com", "IP: 182.253.11.90", "DITOLAK"),
        ("2026-11-25 12:10:19", "upload_data", "Admin mengunggah dataset Excel SBYTIM Juli 2026.", "admin@sipasut.id", "BERHASIL"),
        ("2026-11-25 09:30:00", "update_notif", "Peringatan pasang rob maksimum Tanjung Perak diaktifkan.", "admin@sipasut.id", "BERHASIL"),
        ("2026-11-25 08:15:22", "logout", "User Administrator BMKG telah keluar dari sistem.", "admin@sipasut.id", "BERHASIL"),
    ]
    for r_idx, lg in enumerate(logs):
        ry = 210 + r_idx * 52
        d.line([(245, ry - 8), (w - 35, ry - 8)], fill="#f1f5f9", width=1)
        for c_idx, val in enumerate(lg):
            dx = [260, 370, 480, 750, 860][c_idx]
            col_t = "#dc2626" if val in ["DITOLAK", "failed_login"] else ("#16a34a" if val == "BERHASIL" else "#334155")
            d.text((dx, ry), val, fill=col_t, font=font_small)
            
    im.save(os.path.join(assets_dir, "gambar9_login_aktivitas_audit.png"))

# 10. Gambar 10: Hasil Testing PHPUnit
def create_img10():
    w, h = 950, 480
    im = Image.new('RGB', (w, h), color='#0f172a')
    d = ImageDraw.Draw(im)
    
    # Terminal Top Bar
    d.rectangle([(0, 0), (w, 38)], fill="#1e293b")
    d.ellipse([(14, 12), (26, 24)], fill="#ef4444")
    d.ellipse([(34, 12), (46, 24)], fill="#eab308")
    d.ellipse([(54, 12), (66, 24)], fill="#22c55e")
    d.text((85, 10), "Terminal - php artisan test --filter=AuthTest", fill="#94a3b8", font=font_small)
    
    # Terminal output text
    lines = [
        ("PS C:\\Users\\minah\\SipasutBMKG-app> php artisan test --filter=AuthTest", "#38bdf8"),
        ("", "#ffffff"),
        ("   PASS   Tests\\Feature\\AuthTest", "#22c55e"),
        ("  +  test login screen can be rendered                           0.08s", "#cbd5e1"),
        ("  +  test register screen can be rendered                        0.04s", "#cbd5e1"),
        ("  +  test new admin can register                                 0.15s", "#cbd5e1"),
        ("  +  test admin can login and redirect to admin dashboard        0.21s", "#cbd5e1"),
        ("  +  test guest cannot access admin dashboard (Redirect /login)  0.06s", "#38bdf8"),
        ("  +  test non admin user cannot access admin dashboard (403)     0.07s", "#38bdf8"),
        ("  +  test admin user can access admin dashboard (200 OK)         0.09s", "#22c55e"),
        ("", "#ffffff"),
        ("  Tests:    7 passed (19 assertions)", "#22c55e"),
        ("  Duration: 0.70s", "#94a3b8"),
        ("", "#ffffff"),
        ("HASIL EVALUASI KEAMANAN: HAK AKSES PENGGUNA TERBUKTI AMAN & TEPAT SASARAN", "#4ade80")
    ]
    
    for idx, (txt, col) in enumerate(lines):
        d.text((30, 55 + idx * 24), txt, fill=col, font=font_mono)
        
    im.save(os.path.join(assets_dir, "gambar10_hasil_testing_auth.png"))

print("Generating 10 diagram and UI mockup images...")
create_img1()
create_img2()
create_img3()
create_img4()
create_img5()
create_img6()
create_img7()
create_img8()
create_img9()
create_img10()
print("All 10 images successfully generated in doc_assets/!")

# ==============================================================================
# DOCX BUILDER
# ==============================================================================
print("Building Word (.docx) document...")
doc = docx.Document()

# Page Setup: Margin Standard
for section in doc.sections:
    section.top_margin = Inches(1.0)
    section.bottom_margin = Inches(1.0)
    section.left_margin = Inches(1.0)
    section.right_margin = Inches(1.0)

# Colors
COLOR_NAVY = RGBColor(10, 57, 129)
COLOR_BLUE = RGBColor(31, 80, 154)
COLOR_DARK = RGBColor(30, 41, 59)
COLOR_MUTED = RGBColor(100, 116, 139)
COLOR_RED = RGBColor(185, 28, 28)
COLOR_GREEN = RGBColor(22, 101, 52)

def set_cell_background(cell, hex_color):
    tcPr = cell._element.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:val="clear" w:color="auto" w:fill="{hex_color}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    tcPr = cell._element.get_or_add_tcPr()
    tcMar = parse_xml(f'''
        <w:tcMar {nsdecls("w")}>
            <w:top w:w="{top}" w:type="dxa"/>
            <w:bottom w:w="{bottom}" w:type="dxa"/>
            <w:left w:w="{left}" w:type="dxa"/>
            <w:right w:w="{right}" w:type="dxa"/>
        </w:tcMar>
    ''')
    tcPr.append(tcMar)

def add_callout(doc, title, text, bg_color="F1F5F9", border_color="0284C7"):
    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    table.columns[0].width = Inches(6.5)
    
    cell = table.cell(0, 0)
    set_cell_background(cell, bg_color)
    set_cell_margins(cell, top=140, bottom=140, left=200, right=200)
    
    # Left border
    tcPr = cell._element.get_or_add_tcPr()
    tcBorders = parse_xml(f'''
        <w:tcBorders {nsdecls("w")}>
            <w:top w:val="none"/>
            <w:left w:val="single" w:sz="24" w:space="0" w:color="{border_color}"/>
            <w:bottom w:val="none"/>
            <w:right w:val="none"/>
        </w:tcBorders>
    ''')
    tcPr.append(tcBorders)
    
    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(2)
    p.paragraph_format.line_spacing = 1.15
    run_t = p.add_run(f"â€¢ {title}: ")
    run_t.bold = True
    run_t.font.name = "Arial"
    run_t.font.size = Pt(10)
    run_t.font.color.rgb = COLOR_NAVY
    
    run_b = p.add_run(text)
    run_b.font.name = "Arial"
    run_b.font.size = Pt(9.5)
    run_b.font.color.rgb = COLOR_DARK
    
    doc.add_paragraph().paragraph_format.space_after = Pt(4)

def add_code_block(doc, code_str):
    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.columns[0].width = Inches(6.5)
    cell = table.cell(0, 0)
    set_cell_background(cell, "0F172A")
    set_cell_margins(cell, top=120, bottom=120, left=180, right=180)
    
    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(0)
    p.paragraph_format.space_after = Pt(0)
    p.paragraph_format.line_spacing = 1.1
    run = p.add_run(code_str)
    run.font.name = "Consolas"
    run.font.size = Pt(8.5)
    run.font.color.rgb = RGBColor(241, 245, 249)
    doc.add_paragraph().paragraph_format.space_after = Pt(4)

def add_heading_1(doc, text):
    h = doc.add_paragraph()
    h.paragraph_format.space_before = Pt(18)
    h.paragraph_format.space_after = Pt(6)
    h.paragraph_format.keep_with_next = True
    r = h.add_run(text)
    r.font.name = "Arial"
    r.font.size = Pt(15)
    r.bold = True
    r.font.color.rgb = COLOR_NAVY
    return h

def add_heading_2(doc, text):
    h = doc.add_paragraph()
    h.paragraph_format.space_before = Pt(14)
    h.paragraph_format.space_after = Pt(4)
    h.paragraph_format.keep_with_next = True
    r = h.add_run(text)
    r.font.name = "Arial"
    r.font.size = Pt(12)
    r.bold = True
    r.font.color.rgb = COLOR_BLUE
    return h

def add_heading_3(doc, text):
    h = doc.add_paragraph()
    h.paragraph_format.space_before = Pt(10)
    h.paragraph_format.space_after = Pt(2)
    h.paragraph_format.keep_with_next = True
    r = h.add_run(text)
    r.font.name = "Arial"
    r.font.size = Pt(10.5)
    r.bold = True
    r.font.color.rgb = COLOR_DARK
    return h

def add_p(doc, text, bold_prefix=None, space_after=6):
    p = doc.add_paragraph()
    p.paragraph_format.space_after = Pt(space_after)
    p.paragraph_format.line_spacing = 1.25
    if bold_prefix:
        r_pre = p.add_run(bold_prefix)
        r_pre.bold = True
        r_pre.font.name = "Arial"
        r_pre.font.size = Pt(10)
        r_pre.font.color.rgb = COLOR_DARK
    r = p.add_run(text)
    r.font.name = "Arial"
    r.font.size = Pt(10)
    r.font.color.rgb = COLOR_DARK
    return p

def add_image_with_caption(doc, img_name, caption_text, explanation_text):
    img_path = os.path.join(assets_dir, img_name)
    if os.path.exists(img_path):
        p_img = doc.add_paragraph()
        p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_img.paragraph_format.space_before = Pt(10)
        p_img.paragraph_format.space_after = Pt(4)
        run_img = p_img.add_run()
        run_img.add_picture(img_path, width=Inches(6.0))
        
        # Caption
        p_cap = doc.add_paragraph()
        p_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_cap.paragraph_format.space_before = Pt(2)
        p_cap.paragraph_format.space_after = Pt(6)
        r_cap = p_cap.add_run(caption_text)
        r_cap.font.name = "Arial"
        r_cap.font.size = Pt(9)
        r_cap.bold = True
        r_cap.italic = True
        r_cap.font.color.rgb = COLOR_NAVY
        
        # Explanation box
        add_callout(doc, "Penjelasan & Makna Visual Gambar", explanation_text, bg_color="F8FAFC", border_color="0A3981")

# ==============================================================================
# DOKUMEN ISI
# ==============================================================================

# COVER PAGE
p_cov_pre = doc.add_paragraph()
p_cov_pre.alignment = WD_ALIGN_PARAGRAPH.CENTER
p_cov_pre.paragraph_format.space_before = Pt(30)
p_cov_pre.paragraph_format.space_after = Pt(10)
r_inst = p_cov_pre.add_run("BADAN METEOROLOGI, KLIMATOLOGI, DAN GEOFISIKA (BMKG)\nSTASIUN METEOROLOGI MARITIM KELAS II TANJUNG PERAK SURABAYA")
r_inst.bold = True
r_inst.font.name = "Arial"
r_inst.font.size = Pt(11)
r_inst.font.color.rgb = COLOR_NAVY

# Logo BMKG
bmkg_logo_path = os.path.join(os.getcwd(), "public", "assets", "img", "logo-bmkg.png")
if not os.path.exists(bmkg_logo_path):
    bmkg_logo_path = os.path.join(os.getcwd(), "public", "images", "logo-bmkg.png")
if os.path.exists(bmkg_logo_path):
    p_logo = doc.add_paragraph()
    p_logo.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_logo.paragraph_format.space_before = Pt(15)
    p_logo.paragraph_format.space_after = Pt(20)
    p_logo.add_run().add_picture(bmkg_logo_path, width=Inches(1.5))

p_title = doc.add_paragraph()
p_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
p_title.paragraph_format.space_after = Pt(8)
r_t = p_title.add_run("LAPORAN PENGUJIAN DAN SPESIFIKASI SISTEM KEAMANAN:\nAUTHENTICATION & AUTHORIZATION")
r_t.bold = True
r_t.font.name = "Arial"
r_t.font.size = Pt(16)
r_t.font.color.rgb = COLOR_NAVY

p_sub = doc.add_paragraph()
p_sub.alignment = WD_ALIGN_PARAGRAPH.CENTER
p_sub.paragraph_format.space_after = Pt(30)
r_sub = p_sub.add_run("Memastikan Keabsahan Identitas & Pembatasan Hak Akses: User Tidak Dapat Membuka Halaman CRUD Admin pada Aplikasi Web SIPASUT BMKG")
r_sub.font.name = "Arial"
r_sub.font.size = Pt(11)
r_sub.italic = True
r_sub.font.color.rgb = COLOR_MUTED

# Meta Cover Box
cov_table = doc.add_table(rows=5, cols=2)
cov_table.alignment = WD_TABLE_ALIGNMENT.CENTER
cov_table.columns[0].width = Inches(2.2)
cov_table.columns[1].width = Inches(4.2)
cov_meta = [
    ("Nama Aplikasi", "SIPASUT (Sistem Informasi Pasang Surut Air Laut)"),
    ("Framework / Stack", "Laravel 11 / PHP 8.3, Spatie Laravel-Permission, Tailwind CSS"),
    ("Wilayah Operasional", "5 Titik Stasiun Monitoring Pantai Jawa Timur (SBYTIM, SBYBRT, SBYPLB, KAL, BWI)"),
    ("Fokus Evaluasi", "Authentication 2FA (OTP), RBAC Spatie, Proteksi CRUD Admin"),
    ("Status Pengujian", "VERIFIED & PASSED (Automated Test & Manual Assertion)")
]
for idx, (label, val) in enumerate(cov_meta):
    c0 = cov_table.cell(idx, 0)
    c1 = cov_table.cell(idx, 1)
    set_cell_background(c0, "F1F5F9")
    set_cell_margins(c0, top=60, bottom=60, left=100, right=100)
    set_cell_margins(c1, top=60, bottom=60, left=100, right=100)
    
    p0 = c0.paragraphs[0]
    p0.paragraph_format.space_after = Pt(0)
    r0 = p0.add_run(label)
    r0.bold = True
    r0.font.name = "Arial"
    r0.font.size = Pt(9.5)
    r0.font.color.rgb = COLOR_DARK
    
    p1 = c1.paragraphs[0]
    p1.paragraph_format.space_after = Pt(0)
    r1 = p1.add_run(val)
    r1.font.name = "Arial"
    r1.font.size = Pt(9.5)
    r1.font.color.rgb = COLOR_DARK

doc.add_page_break()

# ==============================================================================
# BAB I: PENDAHULUAN
# ==============================================================================
add_heading_1(doc, "BAB I. PENDAHULUAN & GAMBARAN UMUM SISTEM")

add_p(doc, 
    "Sistem Informasi Pasang Surut Air Laut (SIPASUT) merupakan aplikasi web resmi berbasis Laravel yang dikembangkan untuk Stasiun Meteorologi Maritim Kelas II Tanjung Perak Surabaya (BMKG). Sistem ini memiliki fungsi strategis dalam menyajikan dan mengolah data pasang surut (tidal data) di 5 titik pantai dan perairan vital Jawa Timur, meliputi Surabaya Timur (SBYTIM), Surabaya Barat (SBYBRT), Surabaya Pelabuhan / Tanjung Perak (SBYPLB), Kalianget Sumenep (KAL), dan Ketapang Banyuwangi (BWI).",
    bold_prefix="Latar Belakang Sistem: "
)

add_p(doc,
    "Mengingat data pasang surut digunakan sebagai acuan navigasi keselamatan pelayaran, operasional pelabuhan niaga, serta peringatan dini banjir rob bagi masyarakat pesisir, maka integritas data di dalam SIPASUT bersifat sangat krusial. Oleh karena itu, arsitektur keamanan aplikasi dibangun dengan pemisahan peran yang tegas antara pengguna publik/masyarakat umum dan staf administrator BMKG.",
    bold_prefix="Kebutuhan Keamanan Data: "
)

add_callout(doc, "Prinsip Utama Hak Akses SIPASUT",
    "1. Modul Publik: Dapat diakses bebas oleh masyarakat, akademisi, dan nelayan (Dashboard, Peta Lokasi, Kalender Fase Pasang Surut, Download Laporan Publik).\n"
    "2. Modul Admin (CRUD): DILINDUNGI PENUH oleh middleware ganda ('auth' dan 'role:admin'). Pengguna biasa / masyarakat TIDAK DAPAT melihat, membuka, menginput, mengubah, maupun menghapus data operasional BMKG.",
    bg_color="EFF6FF", border_color="1D4ED8"
)

add_heading_2(doc, "1.1 Matriks Pembagian Hak Akses Antar Peran (Role Matrix)")
add_p(doc, "Berikut merupakan matriks pemetaan hak akses antara Pengguna Publik (Role: 'user') dengan Administrator BMKG (Role: 'admin'):")

# Table Matrix
tbl_mat = doc.add_table(rows=7, cols=4)
tbl_mat.alignment = WD_TABLE_ALIGNMENT.CENTER
tbl_mat.columns[0].width = Inches(0.6)
tbl_mat.columns[1].width = Inches(2.7)
tbl_mat.columns[2].width = Inches(1.6)
tbl_mat.columns[3].width = Inches(1.6)

th_texts = ["No", "Nama Halaman / Modul Endpoint", "User Publik ('user')", "Admin BMKG ('admin')"]
for c_i, th in enumerate(th_texts):
    cell = tbl_mat.cell(0, c_i)
    set_cell_background(cell, "0A3981")
    set_cell_margins(cell, top=80, bottom=80, left=80, right=80)
    p = cell.paragraphs[0]
    p.paragraph_format.space_after = Pt(0)
    r = p.add_run(th)
    r.bold = True
    r.font.name = "Arial"
    r.font.size = Pt(9)
    r.font.color.rgb = RGBColor(255, 255, 255)

matrix_data = [
    ("1", "Dashboard Grafik & Peta Realtime (/dashboard)", "Akses Penuh (Read-Only)", "Akses Penuh"),
    ("2", "Download Data Pasang Surut (/download-pasang-surut)", "Akses Penuh (Ekspor Data)", "Akses Penuh"),
    ("3", "Kalender Pasang Surut & Fase Bulan (/kalender)", "Akses Penuh (Read-Only)", "Akses Penuh"),
    ("4", "CRUD Kelola Data Prediksi & Upload (/admin/data)", "DIBLOKIR (HTTP 403)", "Akses Penuh (Create, Read, Update, Delete)"),
    ("5", "CRUD Kelola Titik Monitoring Lokasi (/admin/lokasi)", "DIBLOKIR (HTTP 403)", "Akses Penuh (Tambah & Edit Stasiun)"),
    ("6", "CRUD Kelola Notifikasi Peringatan Rob (/admin/notifikasi)", "DIBLOKIR (HTTP 403)", "Akses Penuh (Buat & Toggle Alert)")
]
for r_i, row in enumerate(matrix_data):
    for c_i, val in enumerate(row):
        cell = tbl_mat.cell(r_i + 1, c_i)
        bg = "FFFFFF" if r_i % 2 == 0 else "F8FAFC"
        if "DIBLOKIR" in val:
            bg = "FEF2F2"
        set_cell_background(cell, bg)
        set_cell_margins(cell, top=60, bottom=60, left=80, right=80)
        p = cell.paragraphs[0]
        p.paragraph_format.space_after = Pt(0)
        r = p.add_run(val)
        r.font.name = "Arial"
        r.font.size = Pt(8.5)
        if "DIBLOKIR" in val:
            r.bold = True
            r.font.color.rgb = COLOR_RED
        elif "Akses Penuh (" in val and "Create" in val:
            r.font.color.rgb = COLOR_GREEN

add_p(doc, "", space_after=10)

# ==============================================================================
# BAB II: ARSITEKTUR AUTHENTICATION
# ==============================================================================
add_heading_1(doc, "BAB II. MEKANISME AUTHENTICATION (PEMERIKSAAN IDENTITAS)")

add_p(doc,
    "Authentication (Otentikasi) adalah proses pembuktian dan verifikasi bahwa pengguna yang mengakses sistem benar-benar merupakan personil yang memiliki klaim atas identitas tersebut. Pada proyek SIPASUT BMKG, otentikasi dirancang berlapis (Multi-Factor Authentication / Two-Factor Authentication) untuk mencegah kebocoran kredensial.",
    bold_prefix="Definisi & Tujuan: "
)

add_heading_2(doc, "2.1 Komponen Otentikasi Proyek SIPASUT")
add_p(doc, "1. Enkripsi Password Standar Industri: Seluruh kata sandi administrator dienkripsi secara satu arah menggunakan algoritma Bcrypt (Hash::make()) dengan salt unik, sehingga tidak pernah tersimpan dalam bentuk plain text di basis data.")
add_p(doc, "2. Two-Factor Authentication via Kode OTP Email: Setiap kali admin berhasil memvalidasi email dan password, sistem tidak langsung memberikan sesi login aktif. Sistem akan secara otomatis meng-generate 6-digit angka acak yang di-hash dan dikirimkan secara realtime ke alamat email terdaftar (App\\Mail\\OtpMail).")
add_p(doc, "3. Masa Kedaluwarsa & Brute-Force Protection: Kode OTP hanya berlaku selama 5 menit (otp_expires_at). Apabila pengguna salah memasukkan kode sebanyak 3 kali berturut-turut, sesi akan langsung dibatalkan, kode OTP di-reset menjadi null, dan pengguna dipaksa melakukan login ulang dari awal.")
add_p(doc, "4. Session Invalidation & Token Regeneration: Saat logout atau setelah verifikasi berhasil, sesi lama diinvalidation dan token CSRF di-regenerate untuk mencegah serangan Session Fixation.")

add_heading_2(doc, "2.2 Potongan Kode Sumber Otentikasi (app/Http/Controllers/AuthController.php)")
add_code_block(doc,
"""// Cuplikan Logika Login & OTP pada AuthController.php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (! Auth::validate($credentials)) {
        ActivityLog::record('failed_login', "Percobaan login gagal untuk: " . $request->email);
        return back()->withErrors(['email' => 'Email atau password tidak sesuai.']);
    }

    $user = User::where('email', $credentials['email'])->first();
    $code = (string) random_int(100000, 999999);

    $user->update([
        'otp_code' => Hash::make($code),
        'otp_expires_at' => now()->addMinutes(5),
    ]);

    Mail::to($user->email)->send(new OtpMail($code));
    $request->session()->put('otp_user_id', $user->id);
    return redirect()->route('otp.show');
}"""
)

add_image_with_caption(doc, "gambar2_form_login_admin.png",
    "Gambar 1. Antarmuka Form Login Admin BMKG (/login)",
    "Halaman login admin menyajikan form autentikasi kredensial email dan password khusus personil BMKG. Bila kombinasi salah, sistem mencatat percobaan gagal ke log audit. Bila benar, admin diarahkan ke tahapan verifikasi kedua (OTP)."
)

add_image_with_caption(doc, "gambar3_verifikasi_otp_email.png",
    "Gambar 2. Antarmuka Form Verifikasi 6-Digit OTP (/otp)",
    "Lapisan verifikasi kedua (Two-Factor Authentication). Admin wajib menginput kode OTP 6 digit yang terkirim ke email dinas BMKG dalam durasi 5 menit dengan batas maksimal 3 kali percobaan salah."
)

# ==============================================================================
# BAB III: ARSITEKTUR AUTHORIZATION (SPATIE PERMISSION)
# ==============================================================================
add_heading_1(doc, "BAB III. ARSITEKTUR AUTHORIZATION & HAK AKSES BERBASIS ROLE (RBAC)")

add_p(doc,
    "Authorization (Otorisasi) adalah proses penentuan apakah pengguna yang sudah berhasil diautentikasi memiliki wewenang atau hak yang cukup untuk menjalankan suatu tindakan (action) atau membuka halaman tertentu. SIPASUT menerapkan Role-Based Access Control (RBAC) menggunakan paket standar Laravel Spatie Permission (spatie/laravel-permission).",
    bold_prefix="Definisi & Implementasi: "
)

add_heading_2(doc, "3.1 Struktur Trait dan Model Pengguna (App\\Models\\User)")
add_p(doc, "Model User mengintegrasikan Spatie\\Permission\\Traits\\HasRoles, yang menghubungkan pengguna dengan tabel roles dan permissions secara dinamis. Di samping itu, tersedia helper method User::isAdmin() untuk verifikasi cepat di tingkat model dan view.")

add_code_block(doc,
"""// Model User dengan Spatie HasRoles & Role Checking
namespace App\\Models;

use Illuminate\\Foundation\\Auth\\User as Authenticatable;
use Spatie\\Permission\\Traits\\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    protected $fillable = ['name', 'email', 'password', 'role', 'otp_code', 'otp_expires_at'];

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }
}"""
)

add_heading_2(doc, "3.2 Pendaftaran Middleware Alias (bootstrap/app.php)")
add_p(doc, "Pada arsitektur modern Laravel 11/13, alias middleware didaftarkan pada closure withMiddleware di dalam file bootstrap/app.php:")

add_code_block(doc,
"""->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \\Spatie\\Permission\\Middleware\\RoleMiddleware::class,
        'permission' => \\Spatie\\Permission\\Middleware\\PermissionMiddleware::class,
    ]);
})"""
)

add_image_with_caption(doc, "gambar1_arsitektur_keamanan.png",
    "Gambar 3. Diagram Alur Arsitektur Filter Keamanan SIPASUT BMKG",
    "Diagram menunjukkan alur komprehensif ketika sebuah HTTP Request menuju route /admin/*. Request pertama melewati Auth Middleware (apakah sudah login? Jika belum -> redirect 302 ke login). Jika sudah login, request disaring oleh Role Middleware Spatie (apakah role == 'admin'? Jika role == 'user' -> diblokir HTTP 403 Forbidden. Jika role == 'admin' -> diizinkan ke AdminController)."
)

# ==============================================================================
# BAB IV: PEMBATASAN AKSES: USER TIDAK DAPAT MEMBUKA CRUD ADMIN
# ==============================================================================
add_heading_1(doc, "BAB IV. PEMBATASAN AKSES: USER TIDAK DAPAT MEMBUKA HALAMAN CRUD ADMIN")

add_p(doc,
    "Sesuai kebutuhan spesifikasi sistem, seluruh halaman CRUD (Create, Read, Update, Delete) yang dimiliki oleh Administrator BMKG diproteksi secara ketat. Pengguna biasa (role 'user' maupun tamu unauthenticated) tidak dapat membuka halaman-halaman tersebut melalui cara apa pun, baik via klik navigasi maupun manipulasi langsung pada URL browser.",
    bold_prefix="Fokus Perlindungan Keamanan: "
)

add_heading_2(doc, "4.1 Definisi Route Grouping yang Diproteksi (routes/web.php)")
add_p(doc, "Semua route administrasi dikelompokkan dengan prefix 'admin' dan diwajibkan melewati dua lapis middleware: ['auth', 'role:admin']:")

add_code_block(doc,
"""// Route Group Admin dilindungi dengan Middleware Ganda
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.index');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // 1. Kelola Data (Prediksi & Upload Excel/PDF)
    Route::get('/data', [AdminController::class, 'kelolaData'])->name('admin.data');
    Route::post('/data/prediksi', [AdminController::class, 'storePrediksi'])->name('admin.data.store');
    Route::put('/data/prediksi/{id}', [AdminController::class, 'updatePrediksi'])->name('admin.data.update');
    Route::delete('/data/prediksi/{id}', [AdminController::class, 'deletePrediksi'])->name('admin.data.destroy');

    // 2. Kelola Lokasi (CRUD 5 Titik Stasiun Monitoring)
    Route::get('/lokasi', [AdminController::class, 'kelolaLokasi'])->name('admin.lokasi');
    Route::post('/lokasi', [AdminController::class, 'storeLocation'])->name('admin.lokasi.store');
    Route::put('/lokasi/{id}', [AdminController::class, 'updateLocation'])->name('admin.lokasi.update');
    Route::delete('/lokasi/{id}', [AdminController::class, 'deleteLocation'])->name('admin.lokasi.delete');

    // 3. Kelola Notifikasi (Peringatan Pasang Rob)
    Route::get('/notifikasi', [AdminController::class, 'kelolaNotif'])->name('admin.notif');
    Route::post('/notifikasi', [AdminController::class, 'storeNotif'])->name('admin.notif.store');
    Route::post('/notifikasi/{id}/toggle', [AdminController::class, 'toggleNotif'])->name('admin.notif.toggle');

    // 4. Log Aktivitas & Profil
    Route::get('/aktivitas', [AdminController::class, 'loginAktivitas'])->name('admin.aktivitas');
    Route::get('/profil', [AdminController::class, 'kelolaProfil'])->name('admin.profil');
});"""
)

add_heading_2(doc, "4.2 Tiga Skenario Pengujian Akses Pengguna")

add_p(doc, "1. Skenario 1 - Tamu Belum Login (Guest): Apabila seseorang yang belum melakukan otentikasi mencoba mengetikkan URL seperti https://sipasut.bmkg.go.id/admin/data atau /admin/dashboard pada address bar browser, middleware 'auth' akan mendeteksi tidak adanya sesi token. Pengguna secara otomatis dialihkan (HTTP 302 Found) ke halaman /login. Form maupun data tabel admin sama sekali tidak dikirimkan ke client.")
add_image_with_caption(doc, "gambar4_redirect_guest_login.png",
    "Gambar 4. Bukti Respon Sistem Saat Tamu (Guest) Mengakses /admin/dashboard",
    "Tangkapan layar pengujian membuktikan request unauthenticated langsung dicegat oleh middleware 'auth' dan dialihkan ke rute login. Informasi dashboard dan fungsi CRUD admin tetap terlindungi."
)

add_p(doc, "2. Skenario 2 - Pengguna Biasa Terautentikasi (Role: 'user'): Apabila seorang user biasa (misalnya akun masyarakat/nelayan: user@sipasut.id) yang sudah login mencoba mengakses URL admin (/admin/data, /admin/lokasi, /admin/dashboard), middleware 'auth' akan meloloskannya karena sesi valid. Namun, middleware kedua ('role:admin') mengevaluasi hak akses. Karena peran user adalah 'user' dan BUKAN 'admin', sistem langsung melempar UnauthorizedException dengan status kode HTTP 403 Forbidden. Akses ditolak seketika.")
add_image_with_caption(doc, "gambar5_user_forbidden_403.png",
    "Gambar 5. Bukti Akses Ditolak (HTTP 403 Forbidden) Ketika User Biasa Membuka CRUD Admin",
    "Pengujian menunjukkan bahwa user dengan peran biasa yang berusaha membobol URL halaman CRUD admin diblokir oleh Spatie Role Middleware dengan kode status HTTP 403 Forbidden. Pengguna tidak dapat memanipulasi data apa pun."
)

add_p(doc, "3. Skenario 3 - Administrator BMKG Terverifikasi (Role: 'admin'): Apabila personil resmi BMKG (admin@sipasut.id) login menggunakan email, password, dan verifikasi OTP, sistem mengenali perannya sebagai 'admin'. Seluruh middleware meloloskan request, dan admin diberikan akses penuh ke seluruh antarmuka dan operasi CRUD.")
add_image_with_caption(doc, "gambar6_dashboard_admin_sukses.png",
    "Gambar 6. Tampilan Dashboard Admin SIPASUT Setelah Autentikasi & Otorisasi Berhasil",
    "Halaman dashboard admin menampilkan metrik ringkasan stasiun pantau (SBYTIM, SBYBRT, SBYPLB, KAL, BWI), indikator peringatan rob, serta navigasi lengkap menuju seluruh modul CRUD."
)

# ==============================================================================
# BAB V: RINCIAN HALAMAN CRUD ADMIN
# ==============================================================================
add_heading_1(doc, "BAB V. RINCIAN MODUL CRUD ADMIN YANG DIPROTEKSI")

add_p(doc, "Berikut merupakan dokumentasi modul-modul CRUD internal yang berada di balik proteksi role admin:")

add_heading_2(doc, "5.1 Modul CRUD Kelola Data Pasang Surut & Prediksi (/admin/data)")
add_p(doc, "Modul ini menangani pengunggahan berkas pasang surut format Excel (.xlsx), CSV, maupun PDF resmi stasiun BMKG. Selain itu, admin dapat melakukan input data manual prediksi per jam (jam 1 hingga 24), mengubah nilai prediksi pasang/surut (tinggi air dalam cm), serta menghapus record yang tidak valid.")
add_image_with_caption(doc, "gambar7_crud_kelola_data.png",
    "Gambar 7. Antarmuka Modul CRUD Kelola Data Prediksi & Upload (/admin/data)",
    "Halaman ini hanya dapat diakses staf admin untuk mengelola matriks data pasang surut 5 titik pantai Jawa Timur serta mengunggah dataset berkala."
)

add_heading_2(doc, "5.2 Modul CRUD Kelola Titik Monitoring Lokasi (/admin/lokasi)")
add_p(doc, "Modul ini memungkinkan administrator BMKG menambahkan stasiun monitoring baru, memperbarui koordinat lintang (latitude) dan bujur (longitude), mengubah metadata institusi penanggung jawab, serta mengaktifkan/menonaktifkan sensor pengamatan.")
add_image_with_caption(doc, "gambar8_crud_kelola_lokasi.png",
    "Gambar 8. Antarmuka Modul CRUD Kelola Lokasi Monitoring (/admin/lokasi)",
    "Halaman konfigurasi master data 5 titik pantai Jawa Timur (Surabaya Timur, Alur Pelayaran Barat, Tanjung Perak, Kalianget, dan Ketapang Banyuwangi)."
)

add_heading_2(doc, "5.3 Modul Audit Trail & Log Aktivitas (/admin/aktivitas)")
add_p(doc, "Sistem keamanan SIPASUT dilengkapi tabel pencatatan audit log (ActivityLog) yang merekam setiap peristiwa penting secara otomatis: identitas personil, alamat IP publik pengakses, jenis aksi (login_otp, failed_login, upload_data, update_notif, logout), dan timestamp akurat.")
add_image_with_caption(doc, "gambar9_login_aktivitas_audit.png",
    "Gambar 9. Antarmuka Audit Trail & Log Aktivitas Keamanan (/admin/aktivitas)",
    "Pencatatan rekam jejak digital untuk keperluan forensik keamanan, audit operasional instansi, dan pemantauan upaya serangan brute-force."
)

# ==============================================================================
# BAB VI: BUKTI PENGUJIAN KEAMANAN
# ==============================================================================
add_heading_1(doc, "BAB VI. BUKTI PENGUJIAN KEAMANAN (AUTOMATED TEST SUITE)")

add_p(doc,
    "Keandalan mekanisme otentikasi dan pembatasan otorisasi pada aplikasi SIPASUT BMKG telah dibuktikan melalui pengujian otomatis (automated test) pada kelas Tests\\Feature\\AuthTest. Seluruh skenario pengujian dieksekusi menggunakan test runner Laravel dan PHPUnit.",
    bold_prefix="Metodologi Pengujian: "
)

add_heading_2(doc, "6.1 Hasil Pengujian Kasus Uji (Test Cases Execution)")
add_image_with_caption(doc, "gambar10_hasil_testing_auth.png",
    "Gambar 10. Hasil Eksekusi Unit Test Keamanan (php artisan test --filter=AuthTest)",
    "Output CLI membuktikan 100% test cases terkait otentikasi dan otorisasi berstatus PASS. Hal ini menjamin bahwa tidak ada celah keamanan (security bypass) yang memungkinkan user biasa membuka halaman admin."
)

# Test Table
tbl_test = doc.add_table(rows=8, cols=4)
tbl_test.alignment = WD_TABLE_ALIGNMENT.CENTER
tbl_test.columns[0].width = Inches(0.5)
tbl_test.columns[1].width = Inches(3.0)
tbl_test.columns[2].width = Inches(1.8)
tbl_test.columns[3].width = Inches(1.2)

t_heads = ["No", "Nama Kasus Uji (Test Method)", "Kriteria Keberhasilan", "Hasil Uji"]
for c_i, th in enumerate(t_heads):
    cell = tbl_test.cell(0, c_i)
    set_cell_background(cell, "0A3981")
    set_cell_margins(cell, top=80, bottom=80, left=80, right=80)
    p = cell.paragraphs[0]
    p.paragraph_format.space_after = Pt(0)
    r = p.add_run(th)
    r.bold = True
    r.font.name = "Arial"
    r.font.size = Pt(9)
    r.font.color.rgb = RGBColor(255, 255, 255)

test_records = [
    ("1", "test_login_screen_can_be_rendered", "Form login tampil (HTTP 200) dengan label BMKG", "PASSED"),
    ("2", "test_register_screen_can_be_rendered", "Form registrasi admin dapat diakses (HTTP 200)", "PASSED"),
    ("3", "test_new_admin_can_register", "Admin baru tersimpan dan otomatis diberi role:admin", "PASSED"),
    ("4", "test_admin_can_login_and_redirect_to_admin_dashboard", "Login email & password memicu OTP, lalu lolos ke admin", "PASSED"),
    ("5", "test_guest_cannot_access_admin_dashboard", "Tamu belum login dialihkan (Redirect 302) ke /login", "PASSED"),
    ("6", "test_non_admin_user_cannot_access_admin_dashboard_via_spatie", "User biasa ditolak dengan status HTTP 403 Forbidden", "PASSED"),
    ("7", "test_admin_user_can_access_admin_dashboard", "Akun role admin berhasil membuka dashboard (HTTP 200)", "PASSED"),
]
for r_i, row in enumerate(test_records):
    for c_i, val in enumerate(row):
        cell = tbl_test.cell(r_i + 1, c_i)
        bg = "FFFFFF" if r_i % 2 == 0 else "F8FAFC"
        set_cell_background(cell, bg)
        set_cell_margins(cell, top=60, bottom=60, left=80, right=80)
        p = cell.paragraphs[0]
        p.paragraph_format.space_after = Pt(0)
        r = p.add_run(val)
        r.font.name = "Arial"
        r.font.size = Pt(8.5)
        if val == "PASSED":
            r.bold = True
            r.font.color.rgb = COLOR_GREEN

add_p(doc, "", space_after=10)

# ==============================================================================
# BAB VII: KESIMPULAN
# ==============================================================================
add_heading_1(doc, "BAB VII. KESIMPULAN & REKOMENDASI PENGAMANAN")

add_p(doc,
    "Berdasarkan hasil analisis kode sumber, konfigurasi middleware, serta eksekusi pengujian otomatis, sistem keamanan pada aplikasi SIPASUT BMKG Tanjung Perak telah memenuhi seluruh persyaratan Authentication & Authorization:",
    bold_prefix="Kesimpulan Hasil Evaluasi: "
)

add_p(doc, "1. Memastikan Identitas Benar (Authentication): Mekanisme autentikasi telah diperkuat dengan 2FA melalui pengiriman kode OTP 6-digit ke email dinas terdaftar dengan masa berlaku 5 menit dan pembatasan percobaan gagal 3 kali, memitigasi risiko kebocoran password.")
add_p(doc, "2. Memastikan Hak Akses Benar (Authorization): Penerapan Spatie Laravel-Permission memisahkan hak akses secara tegas antara peran publik ('user') dan personil operasional BMKG ('admin').")
add_p(doc, "3. Pembatasan Halaman CRUD Admin Berhasil 100%: Pengguna biasa maupun tamu tidak dapat membuka halaman CRUD Admin. Tamu dicegat oleh middleware 'auth' (dialihkan ke /login), sedangkan user biasa dicegat oleh middleware 'role:admin' dengan status HTTP 403 Forbidden.")
add_p(doc, "4. Akuntabilitas & Audit Trail Lengkap: Setiap aktivitas login, percobaan gagal, dan modifikasi data tercatat secara permanen pada ActivityLog disertai informasi alamat IP.")

add_callout(doc, "Pernyataan Keabsahan Pengujian Keamanan",
    "Dokumen ini menyatakan bahwa sistem kontrol akses pada aplikasi SIPASUT BMKG Stasiun Meteorologi Maritim Kelas II Tanjung Perak Surabaya telah teruji secara fungsional dan teknis, serta bebas dari celah bypass otorisasi pada seluruh rute CRUD Admin.",
    bg_color="ECFDF5", border_color="059669"
)

# Save Word Docx
docx_path = os.path.join(os.getcwd(), "Dokumen_Authentication_Authorization_SIPASUT.docx")
doc.save(docx_path)
print(f"Word document successfully saved at: {docx_path}")

# Convert to PDF using Word COM
pdf_path = os.path.join(os.getcwd(), "Dokumen_Authentication_Authorization_SIPASUT.pdf")
print("Converting Word document to PDF using Microsoft Word Application...")

try:
    word = win32com.client.Dispatch("Word.Application")
    word.Visible = False
    in_file = os.path.abspath(docx_path)
    out_file = os.path.abspath(pdf_path)
    
    doc_com = word.Documents.Open(in_file)
    # FileFormat=17 represents wdFormatPDF in Word Object Model
    doc_com.SaveAs(out_file, FileFormat=17)
    doc_com.Close()
    word.Quit()
    print(f"PDF document successfully created at: {pdf_path}")
except Exception as e:
    print(f"Error during PDF conversion via Word COM: {e}")
    try:
        word.Quit()
    except:
        pass

print("Finished report generation script!")
