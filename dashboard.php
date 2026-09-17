<?php
session_start();
require __DIR__ . '/config/koneksi.php';

// =========================================================
// CEK LOGIN — sesuai session yang di-set di login.php
// =========================================================
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$nama_admin = $_SESSION['admin_nama'] ?? 'Admin';

// =========================================================
// AMBIL DATA STATISTIK
// Sesuaikan nama tabel/kolom kalau struktur DB kamu beda.
// Dibungkus try-catch supaya tidak error kalau tabel belum ada.
// =========================================================
function getCount(PDO $pdo, string $sql) {
    try {
        $stmt = $pdo->query($sql);
        $val  = $stmt->fetchColumn();
        return $val !== false ? $val : 0;
    } catch (PDOException $e) {
        return 0;
    }
}

$totalStasiun    = getCount($pdo, "SELECT COUNT(*) FROM stasiun WHERE status = 'aktif'");
$totalData       = getCount($pdo, "SELECT COUNT(*) FROM data_pasang_surut");
$totalPeringatan = getCount($pdo, "SELECT COUNT(*) FROM peringatan WHERE status = 'aktif'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard Admin BMKG — Monitoring Pasang Surut</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<!-- Dashboard TIDAK memuat style.css (punya halaman login),
     karena class .brand-mark di sana konflik dengan navbar dashboard.
     Semua style dashboard sudah lengkap di dashboard.css. -->
<link rel="stylesheet" href="assets/css/dashboard.css?v=4">
</head>
<body class="dashboard-body">

<!-- ================= NAVBAR ================= -->
<div class="navbar">
    <div class="brand-mark">
        <img src="assets/img/logo-bmkg.png" alt="Logo BMKG">
        <div>
            <div class="brand-name">BMKG</div>
            <div class="brand-sub">Monitoring Pasang Surut</div>
        </div>
    </div>

    <ul class="nav-links">
        <li><a href="dashboard.php" class="active">Dashboard</a></li>
        <li><a href="data_pasang_surut.php">Data Pasang Surut</a></li>
        <li><a href="lokasi.php">Lokasi</a></li>
        <li><a href="log_aktivitas.php">Log Aktivitas</a></li>
        <li><a href="notifikasi.php">Notifikasi</a></li>
    </ul>

    <div class="profile">
        <div class="avatar">👤</div>
        <div class="info">
            <p class="profile-name"><?= htmlspecialchars($nama_admin) ?></p>
            <p class="profile-role">Petugas</p>
        </div>
        <a href="logout.php" class="logout-link">Keluar</a>
    </div>
</div>

<!-- ================= CONTENT ================= -->
<div class="content">

    <!-- Welcome Banner -->
    <div class="banner">
        <div>
            <h2>Selamat datang, <?= htmlspecialchars($nama_admin) ?>!</h2>
            <p>Kelola informasi pasang surut air laut saat ini</p>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="icon blue">📍</div>
            <div>
                <div class="label">Total Stasiun</div>
                <div class="value"><?= $totalStasiun ?></div>
                <div class="sub">● Stasiun Aktif</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="icon indigo">🗄️</div>
            <div>
                <div class="label">Data Pasang Surut</div>
                <div class="value"><?= $totalData ?></div>
                <div class="sub muted">Data Tersimpan</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="icon red">⚠️</div>
            <div>
                <div class="label">Peringatan Aktif</div>
                <div class="value"><?= $totalPeringatan ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="icon sky">🌤️</div>
            <div>
                <div class="label">Surabaya</div>
                <div class="value">28°C</div>
                <div class="sub muted">Cerah Berawan</div>
            </div>
        </div>
    </div>

    <!-- Menu Section -->
    <div class="section-title">Kelola Data Sistem</div>
    <div class="menu-grid">

        <div class="menu-card">
            <div class="icon-circle">🌊</div>
            <div>
                <h3>Kelola Data Pasang Surut</h3>
                <p>Tambah, ubah, hapus, dan kelola data pasang surut dari setiap stasiun.</p>
                <a href="data_pasang_surut.php" class="btn">Kelola Data ›</a>
            </div>
        </div>

        <div class="menu-card">
            <div class="icon-circle">🗺️</div>
            <div>
                <h3>Kelola Lokasi Stasiun</h3>
                <p>Tambah, ubah, hapus, dan kelola informasi lokasi stasiun monitoring.</p>
                <a href="lokasi.php" class="btn">Kelola Data ›</a>
            </div>
        </div>

        <div class="menu-card">
            <div class="icon-circle">📅</div>
            <div>
                <h3>Kelola Prediksi</h3>
                <p>Kelola perkiraan pasang dan surut untuk waktu mendatang.</p>
                <a href="prediksi.php" class="btn">Kelola Data ›</a>
            </div>
        </div>

        <div class="menu-card">
            <div class="icon-circle">🚨</div>
            <div>
                <h3>Kelola Peringatan</h3>
                <p>Kelola peringatan dini terkait kondisi pasang surut.</p>
                <a href="peringatan.php" class="btn">Kelola Data ›</a>
            </div>
        </div>

    </div>
</div>

</body>
</html>