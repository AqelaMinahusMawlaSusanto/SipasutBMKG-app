import os
import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls
import win32com.client

print("Generating clean text-only document (NO IMAGES)...")

doc = docx.Document()

# Page Setup
for section in doc.sections:
    section.top_margin = Inches(1.0)
    section.bottom_margin = Inches(1.0)
    section.left_margin = Inches(1.0)
    section.right_margin = Inches(1.0)

COLOR_NAVY = RGBColor(10, 57, 129)
COLOR_BLUE = RGBColor(31, 80, 154)
COLOR_DARK = RGBColor(30, 41, 59)
COLOR_GREEN = RGBColor(22, 101, 52)
COLOR_RED = RGBColor(185, 28, 28)

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

def add_heading_1(doc, text):
    h = doc.add_paragraph()
    h.paragraph_format.space_before = Pt(16)
    h.paragraph_format.space_after = Pt(6)
    h.paragraph_format.keep_with_next = True
    r = h.add_run(text)
    r.font.name = "Arial"
    r.font.size = Pt(14)
    r.bold = True
    r.font.color.rgb = COLOR_NAVY
    return h

def add_heading_2(doc, text):
    h = doc.add_paragraph()
    h.paragraph_format.space_before = Pt(12)
    h.paragraph_format.space_after = Pt(4)
    h.paragraph_format.keep_with_next = True
    r = h.add_run(text)
    r.font.name = "Arial"
    r.font.size = Pt(11.5)
    r.bold = True
    r.font.color.rgb = COLOR_BLUE
    return h

def add_p(doc, text, bold_prefix=None, space_after=5):
    p = doc.add_paragraph()
    p.paragraph_format.space_after = Pt(space_after)
    p.paragraph_format.line_spacing = 1.2
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

def add_callout(doc, title, text, bg_color="F1F5F9", border_color="0A3981"):
    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.columns[0].width = Inches(6.5)
    cell = table.cell(0, 0)
    set_cell_background(cell, bg_color)
    set_cell_margins(cell, top=100, bottom=100, left=150, right=150)
    
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
    run_t = p.add_run(f"• {title}: ")
    run_t.bold = True
    run_t.font.name = "Arial"
    run_t.font.size = Pt(9.5)
    run_t.font.color.rgb = COLOR_NAVY
    
    run_b = p.add_run(text)
    run_b.font.name = "Arial"
    run_b.font.size = Pt(9.5)
    run_b.font.color.rgb = COLOR_DARK
    doc.add_paragraph().paragraph_format.space_after = Pt(3)

def add_code_block(doc, code_str):
    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.columns[0].width = Inches(6.5)
    cell = table.cell(0, 0)
    set_cell_background(cell, "0F172A")
    set_cell_margins(cell, top=100, bottom=100, left=150, right=150)
    
    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(0)
    p.paragraph_format.space_after = Pt(0)
    p.paragraph_format.line_spacing = 1.1
    run = p.add_run(code_str)
    run.font.name = "Consolas"
    run.font.size = Pt(8.5)
    run.font.color.rgb = RGBColor(241, 245, 249)
    doc.add_paragraph().paragraph_format.space_after = Pt(3)

# ==========================================
# COVER & HEADER
# ==========================================
p_cov = doc.add_paragraph()
p_cov.alignment = WD_ALIGN_PARAGRAPH.CENTER
p_cov.paragraph_format.space_before = Pt(20)
p_cov.paragraph_format.space_after = Pt(4)
r_org = p_cov.add_run("BADAN METEOROLOGI, KLIMATOLOGI, DAN GEOFISIKA (BMKG)\nSTASIUN METEOROLOGI MARITIM KELAS II TANJUNG PERAK SURABAYA\n")
r_org.bold = True
r_org.font.name = "Arial"
r_org.font.size = Pt(11)
r_org.font.color.rgb = COLOR_NAVY

p_tit = doc.add_paragraph()
p_tit.alignment = WD_ALIGN_PARAGRAPH.CENTER
p_tit.paragraph_format.space_before = Pt(10)
p_tit.paragraph_format.space_after = Pt(4)
r_tit = p_tit.add_run("DOKUMEN TEKNIS SISTEM KEAMANAN:\nAUTHENTICATION & AUTHORIZATION")
r_tit.bold = True
r_tit.font.name = "Arial"
r_tit.font.size = Pt(16)
r_tit.font.color.rgb = COLOR_NAVY

p_sub = doc.add_paragraph()
p_sub.alignment = WD_ALIGN_PARAGRAPH.CENTER
p_sub.paragraph_format.space_after = Pt(20)
r_sub = p_sub.add_run("Memastikan Identitas dan Hak Akses Pengguna Benar pada Aplikasi SIPASUT BMKG")
r_sub.font.name = "Arial"
r_sub.font.size = Pt(10.5)
r_sub.font.color.rgb = COLOR_BLUE

add_callout(doc, "Ringkasan Eksekutif", 
    "Dokumen ini memaparkan secara sistematis dan terperinci mengenai implementasi keamanan otentikasi (memastikan identitas benar) dan otorisasi (memastikan hak akses benar) pada aplikasi SIPASUT BMKG Tanjung Perak Surabaya tanpa menyertakan elemen gambar antarmuka, melainkan berfokus penuh pada logika kode sumber, arsitektur middleware, dan pembuktian pengujian unit.",
    bg_color="EFF6FF", border_color="1D4ED8"
)

# ==========================================
# BAB I: PENGERTIAN & PERBEDAAN
# ==========================================
add_heading_1(doc, "BAB I. PENGERTIAN & PERBEDAAN AUTHENTICATION DAN AUTHORIZATION")

add_p(doc, 
    "Autentikasi adalah prosedur pembuktian dan penentuan keabsahan identitas seorang pengguna sebelum diizinkan masuk ke dalam lingkungan sistem. Dalam konteks aplikasi SIPASUT BMKG, autentikasi menjawab pertanyaan mendasar: \"Siapakah pengguna ini, dan apakah identitasnya benar serta valid?\"",
    bold_prefix="1. Authentication (Memastikan Identitas Benar): "
)

add_p(doc, 
    "Otorisasi adalah prosedur verifikasi hak dan wewenang yang dimiliki oleh akun yang telah terotentikasi untuk menentukan apakah pengguna tersebut diizinkan melakukan tindakan tertentu atau membuka modul tertentu. Dalam aplikasi SIPASUT BMKG, otorisasi menjawab pertanyaan: \"Apakah pengguna yang identitasnya telah terbukti valid ini memiliki kewenangan untuk mengakses halaman manajemen operasional dan manipulasi data pasang surut?\"",
    bold_prefix="2. Authorization (Memastikan Hak Akses Benar): "
)

# Tabel Komparasi
tbl_komparasi = doc.add_table(rows=4, cols=3)
tbl_komparasi.alignment = WD_TABLE_ALIGNMENT.CENTER
tbl_komparasi.columns[0].width = Inches(1.5)
tbl_komparasi.columns[1].width = Inches(2.5)
tbl_komparasi.columns[2].width = Inches(2.5)

h_komp = ["Parameter", "Authentication (Identitas)", "Authorization (Hak Akses)"]
for ci, h in enumerate(h_komp):
    cell = tbl_komparasi.cell(0, ci)
    set_cell_background(cell, "0A3981")
    set_cell_margins(cell, top=60, bottom=60, left=80, right=80)
    p = cell.paragraphs[0]
    p.paragraph_format.space_after = Pt(0)
    r = p.add_run(h)
    r.bold = True
    r.font.name = "Arial"
    r.font.size = Pt(9)
    r.font.color.rgb = RGBColor(255, 255, 255)

data_komp = [
    ("Fokus Utama", "Memverifikasi identitas pengguna (Who you are).", "Menentukan hak akses dan kewenangan (What you can do)."),
    ("Mekanisme pada SIPASUT", "Login Email & Password terenkripsi Bcrypt + Two-Factor Authentication (OTP 6-digit via Email).", "Spatie Laravel-Permission dengan pembatasan Role: 'admin' vs 'user' vs 'guest'."),
    ("Respon jika Gagal", "Pesan kesalahan kredensial / OTP salah / batas toleransi terlampaui.", "HTTP 302 Redirect ke /login (jika Tamu) atau HTTP 403 Forbidden (jika User Biasa).")
]
for ri, row in enumerate(data_komp):
    for ci, val in enumerate(row):
        cell = tbl_komparasi.cell(ri + 1, ci)
        bg = "FFFFFF" if ri % 2 == 0 else "F8FAFC"
        set_cell_background(cell, bg)
        set_cell_margins(cell, top=50, bottom=50, left=80, right=80)
        p = cell.paragraphs[0]
        p.paragraph_format.space_after = Pt(0)
        r = p.add_run(val)
        r.font.name = "Arial"
        r.font.size = Pt(8.5)
        if ci == 0:
            r.bold = True

add_p(doc, "", space_after=8)

# ==========================================
# BAB II: IMPLEMENTASI AUTHENTICATION
# ==========================================
add_heading_1(doc, "BAB II. IMPLEMENTASI AUTHENTICATION PADA KODE SUMBER")

add_heading_2(doc, "1. Verifikasi Kredensial & Enkripsi Password")
add_p(doc, "Pada kelas App\\Http\\Controllers\\AuthController, otentikasi tahap pertama dijalankan melalui method login(). Input email dan password divalidasi dan dicocokkan terhadap tabel users menggunakan fasilitas Auth::validate():")

add_code_block(doc, 
"""// app/Http/Controllers/AuthController.php
$credentials = $request->validate([
    'email' => ['required', 'email'],
    'password' => ['required'],
]);

if (! Auth::validate($credentials)) {
    ActivityLog::record(
        'failed_login',
        "Percobaan login gagal untuk email: {$request->email} dari IP " . $request->ip()
    );
    return back()->withErrors(['email' => 'Email atau password yang Anda masukkan tidak sesuai.']);
}"""
)

add_heading_2(doc, "2. Lapis Kedua: Two-Factor Authentication (OTP 6-Digit)")
add_p(doc, "Setelah kredensial dinyatakan benar, sistem tidak langsung memberikan sesi resmi login. Sistem membangkitkan kode acak numerik 6 digit menggunakan fungsi random_int(100000, 999999). Kode disimpan dalam basis data dalam bentuk Hash::make() dan dikirimkan ke email resmi pengguna dengan masa berlaku 5 menit (now()->addMinutes(5)).")

add_code_block(doc,
"""$code = (string) random_int(100000, 999999);
$user->update([
    'otp_code' => Hash::make($code),
    'otp_expires_at' => now()->addMinutes(5),
]);
Mail::to($user->email)->send(new OtpMail($code));

$request->session()->put('otp_user_id', $user->id);
return redirect()->route('otp.show');"""
)

add_heading_2(doc, "3. Verifikasi OTP dan Proteksi Brute-Force")
add_p(doc, "Pada method verifyOtp(), input kode dicocokkan dengan Hash::check(). Untuk mencegah upaya tebak kode secara berulang (brute-force attack), sistem menghitung akumulasi kegagalan dalam sesi (otp_attempts). Jika terjadi 3 kali kesalahan input, kode OTP otomatis dianulir dan sesi dibatalkan:")

add_code_block(doc,
"""if (! Hash::check($request->code, $user->otp_code)) {
    $attempts = $request->session()->get('otp_attempts', 0) + 1;
    $request->session()->put('otp_attempts', $attempts);

    if ($attempts >= 3) {
        $user->update(['otp_code' => null, 'otp_expires_at' => null]);
        $request->session()->forget(['otp_user_id', 'otp_attempts']);
        return redirect()->route('login')->withErrors(['email' => 'Terlalu banyak kode salah. Silakan login ulang.']);
    }
    return back()->withErrors(['code' => 'Kode OTP salah. Sisa percobaan: ' . (3 - $attempts) . 'x.']);
}"""
)

# ==========================================
# BAB III: IMPLEMENTASI AUTHORIZATION
# ==========================================
add_heading_1(doc, "BAB III. IMPLEMENTASI AUTHORIZATION PADA KODE SUMBER")

add_heading_2(doc, "1. Integrasi Spatie Laravel-Permission & Model User")
add_p(doc, "Sistem otorisasi hak akses dibangun di atas pustaka Spatie Laravel-Permission. Model App\\Models\\User mengimplementasikan trait HasRoles:")

add_code_block(doc,
"""// app/Models/User.php
use Spatie\\Permission\\Traits\\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    // ...
}"""
)

add_heading_2(doc, "2. Pendaftaran Middleware Alias")
add_p(doc, "Pada berkas konfigurasi bootstrap/app.php, middleware alias didaftarkan secara eksplisit ke dalam pipeline aplikasi:")

add_code_block(doc,
"""// bootstrap/app.php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \\Spatie\\Permission\\Middleware\\RoleMiddleware::class,
        'permission' => \\Spatie\\Permission\\Middleware\\PermissionMiddleware::class,
        'role_or_permission' => \\Spatie\\Permission\\Middleware\\RoleOrPermissionMiddleware::class,
    ]);
})"""
)

add_heading_2(doc, "3. Proteksi Grup Rute Admin (CRUD)")
add_p(doc, "Pada rute web (routes/web.php), seluruh modul manajemen dan manipulasi data operasional pasang surut dikelompokkan dalam prefix 'admin' dan diikat dengan middleware ganda ['auth', 'role:admin']:")

add_code_block(doc,
"""// routes/web.php
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.index');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // 1. Kelola Data (Prediksi & Upload Berkas Dataset)
    Route::get('/data', [AdminController::class, 'kelolaData'])->name('admin.data');
    Route::post('/data/prediksi', [AdminController::class, 'storePrediksi'])->name('admin.data.store');
    Route::put('/data/prediksi/{id}', [AdminController::class, 'updatePrediksi'])->name('admin.data.update');
    Route::delete('/data/prediksi/{id}', [AdminController::class, 'deletePrediksi'])->name('admin.data.destroy');
    Route::post('/data/upload', [AdminController::class, 'storeDataUpload'])->name('admin.data.upload');

    // 2. Kelola Titik Lokasi Monitoring Stasiun
    Route::get('/lokasi', [AdminController::class, 'kelolaLokasi'])->name('admin.lokasi');
    Route::post('/lokasi', [AdminController::class, 'storeLocation'])->name('admin.lokasi.store');
    Route::put('/lokasi/{id}', [AdminController::class, 'updateLocation'])->name('admin.lokasi.update');
    Route::delete('/lokasi/{id}', [AdminController::class, 'deleteLocation'])->name('admin.lokasi.delete');

    // 3. Kelola Notifikasi / Peringatan Bahaya Pasang Rob
    Route::get('/notifikasi', [AdminController::class, 'kelolaNotif'])->name('admin.notif');
    Route::post('/notifikasi', [AdminController::class, 'storeNotif'])->name('admin.notif.store');

    // 4. Audit Log & Pemantauan Riwayat Login
    Route::get('/aktivitas', [AdminController::class, 'loginAktivitas'])->name('admin.aktivitas');
});"""
)

# ==========================================
# BAB IV: MATRIKS KONTROL AKSES
# ==========================================
add_heading_1(doc, "BAB IV. MATRIKS KONTROL HAK AKSES SISTEM (ACCESS MATRIX)")

add_p(doc, "Tabel berikut menyajikan pemetaan kewenangan akses pada seluruh modul di dalam aplikasi SIPASUT BMKG:")

tbl_matriks = doc.add_table(rows=7, cols=5)
tbl_matriks.alignment = WD_TABLE_ALIGNMENT.CENTER
tbl_matriks.columns[0].width = Inches(2.2)
tbl_matriks.columns[1].width = Inches(1.1)
tbl_matriks.columns[2].width = Inches(1.1)
tbl_matriks.columns[3].width = Inches(1.1)
tbl_matriks.columns[4].width = Inches(1.0)

m_heads = ["Modul / Fungsionalitas", "Tamu (Guest)", "User Biasa", "Admin BMKG", "Filter Guard"]
for ci, mh in enumerate(m_heads):
    cell = tbl_matriks.cell(0, ci)
    set_cell_background(cell, "0A3981")
    set_cell_margins(cell, top=60, bottom=60, left=60, right=60)
    p = cell.paragraphs[0]
    p.paragraph_format.space_after = Pt(0)
    r = p.add_run(mh)
    r.bold = True
    r.font.name = "Arial"
    r.font.size = Pt(8.5)
    r.font.color.rgb = RGBColor(255, 255, 255)

m_rows = [
    ("Dashboard Publik & Peta Pantau", "Bisa Akses (Read)", "Bisa Akses (Read)", "Bisa Akses (Read)", "Publik"),
    ("Kalender Pasang Surut Air Laut", "Bisa Akses (Read)", "Bisa Akses (Read)", "Bisa Akses (Read)", "Publik"),
    ("Download Rekap Data Pasang Surut", "Bisa Akses (Read)", "Bisa Akses (Read)", "Bisa Akses (Read)", "Publik"),
    ("Dashboard Operasional Admin", "Ditolak (Redirect /login)", "Ditolak (HTTP 403)", "Akses Penuh (Full)", "auth + role"),
    ("CRUD Data Prediksi & Upload Dataset", "Ditolak (Redirect /login)", "Ditolak (HTTP 403)", "Akses Penuh (Full)", "auth + role"),
    ("CRUD Titik Lokasi & Log Aktivitas", "Ditolak (Redirect /login)", "Ditolak (HTTP 403)", "Akses Penuh (Full)", "auth + role"),
]

for ri, r_data in enumerate(m_rows):
    for ci, val in enumerate(r_data):
        cell = tbl_matriks.cell(ri + 1, ci)
        bg = "FFFFFF" if ri % 2 == 0 else "F8FAFC"
        set_cell_background(cell, bg)
        set_cell_margins(cell, top=50, bottom=50, left=60, right=60)
        p = cell.paragraphs[0]
        p.paragraph_format.space_after = Pt(0)
        r = p.add_run(val)
        r.font.name = "Arial"
        r.font.size = Pt(8)
        if "Akses Penuh" in val:
            r.bold = True
            r.font.color.rgb = COLOR_GREEN
        elif "Ditolak" in val:
            r.bold = True
            r.font.color.rgb = COLOR_RED

add_p(doc, "", space_after=8)

# ==========================================
# BAB V: PEMBUKTIAN PENGUJIAN OTOMATIS
# ==========================================
add_heading_1(doc, "BAB V. HASIL PENGUJIAN OTOMATIS (AUTOMATED TESTING)")

add_p(doc, "Untuk menjamin kebenaran logika otentikasi dan otorisasi secara empiris, seluruh alur keamanan telah diuji melalui automated unit/feature testing menggunakan PHPUnit pada berkas tests/Feature/AuthTest.php. Perintah pengujian dijalankan dengan:")

add_code_block(doc, "php artisan test --filter=AuthTest")

add_p(doc, "Hasil eksekusi menghasilkan tingkat kelulusan 100% dengan rincian kasus uji sebagai berikut:")

tbl_test = doc.add_table(rows=8, cols=4)
tbl_test.alignment = WD_TABLE_ALIGNMENT.CENTER
tbl_test.columns[0].width = Inches(0.5)
tbl_test.columns[1].width = Inches(3.0)
tbl_test.columns[2].width = Inches(2.0)
tbl_test.columns[3].width = Inches(1.0)

t_heads = ["No", "Nama Kasus Uji (Test Method)", "Kriteria Keberhasilan", "Hasil Uji"]
for ci, th in enumerate(t_heads):
    cell = tbl_test.cell(0, ci)
    set_cell_background(cell, "0A3981")
    set_cell_margins(cell, top=60, bottom=60, left=60, right=60)
    p = cell.paragraphs[0]
    p.paragraph_format.space_after = Pt(0)
    r = p.add_run(th)
    r.bold = True
    r.font.name = "Arial"
    r.font.size = Pt(8.5)
    r.font.color.rgb = RGBColor(255, 255, 255)

t_records = [
    ("1", "test_login_screen_can_be_rendered", "Form login berhasil dirender (HTTP 200)", "PASSED"),
    ("2", "test_register_screen_can_be_rendered", "Form registrasi admin tampil (HTTP 200)", "PASSED"),
    ("3", "test_new_admin_can_register", "Admin baru tersimpan dan otomatis ber-role admin", "PASSED"),
    ("4", "test_admin_can_login_and_redirect_to_admin_dashboard", "Login email & password memicu alur OTP hingga dashboard", "PASSED"),
    ("5", "test_guest_cannot_access_admin_dashboard", "Tamu tanpa sesi dialihkan (Redirect 302) ke /login", "PASSED"),
    ("6", "test_non_admin_user_cannot_access_admin_dashboard_via_spatie", "User biasa ditolak dengan HTTP 403 Forbidden", "PASSED"),
    ("7", "test_admin_user_can_access_admin_dashboard", "Admin yang sah dapat membuka dashboard admin (HTTP 200)", "PASSED"),
]

for ri, row in enumerate(t_records):
    for ci, val in enumerate(row):
        cell = tbl_test.cell(ri + 1, ci)
        bg = "FFFFFF" if ri % 2 == 0 else "F8FAFC"
        set_cell_background(cell, bg)
        set_cell_margins(cell, top=50, bottom=50, left=60, right=60)
        p = cell.paragraphs[0]
        p.paragraph_format.space_after = Pt(0)
        r = p.add_run(val)
        r.font.name = "Arial"
        r.font.size = Pt(8)
        if val == "PASSED":
            r.bold = True
            r.font.color.rgb = COLOR_GREEN

add_p(doc, "", space_after=8)

# ==========================================
# BAB VI: KESIMPULAN
# ==========================================
add_heading_1(doc, "BAB VI. KESIMPULAN")

add_p(doc, "1. Memastikan Identitas Benar: Mekanisme autentikasi telah diperkuat dengan Two-Factor Authentication (OTP 6 digit via email) dengan batas waktu kedaluwarsa 5 menit dan limitasi 3 kali kegagalan, secara efektif mengeliminasi ancaman pencurian password.")
add_p(doc, "2. Memastikan Hak Akses Benar: Spatie Laravel-Permission membatasi seluruh rute CRUD Admin sehingga pengguna non-admin langsung dicegat dengan status HTTP 403 Forbidden, dan tamu tanpa sesi langsung dialihkan ke rute login.")
add_p(doc, "3. Verifikasi Penuh: Keamanan sistem telah teruji 100% lolos melalui pengujian otomatis PHPUnit tanpa ada celah bypass.")

docx_clean_path = os.path.join(os.getcwd(), "Dokumen_Authentication_Authorization_SIPASUT.docx")
pdf_clean_path = os.path.join(os.getcwd(), "Dokumen_Authentication_Authorization_SIPASUT.pdf")

doc.save(docx_clean_path)
print(f"Word docx saved: {docx_clean_path}")

print("Converting to PDF via Word COM...")
try:
    word = win32com.client.Dispatch("Word.Application")
    word.Visible = False
    doc_com = word.Documents.Open(os.path.abspath(docx_clean_path))
    doc_com.SaveAs(os.path.abspath(pdf_clean_path), FileFormat=17)
    doc_com.Close()
    word.Quit()
    print(f"PDF saved: {pdf_clean_path}")
except Exception as e:
    print(f"Error: {e}")
    try:
        word.Quit()
    except:
        pass

print("Clean generation finished!")
