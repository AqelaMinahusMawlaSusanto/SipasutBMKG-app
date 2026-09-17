<?php
session_start();
require __DIR__ . '/config/koneksi.php';

$error   = '';
$sukses  = false;
$nama    = '';
$email   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nama === '' || $email === '' || $password === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        $cek = $pdo->prepare('SELECT id FROM admin WHERE email = ? LIMIT 1');
        $cek->execute([$email]);

        if ($cek->fetch()) {
            $error = 'Email sudah terdaftar. Gunakan email lain.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $simpan = $pdo->prepare('INSERT INTO admin (nama, email, password) VALUES (?, ?, ?)');
            $simpan->execute([$nama, $email, $hash]);
            $sukses = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Buat Akun Admin BMKG — Monitoring Pasang Surut</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="stage">

  <div class="brand-mark">
    <img src="assets/img/logo-bmkg.png" alt="Logo BMKG">
    <div>
      <div class="brand-name">BMKG</div>
      <div class="brand-sub">Monitoring Pasang Surut</div>
    </div>
  </div>

  <div class="stage-grid">

    <div class="headline">
      <h1>Pantau Pasang Surut.<br>Lebih Mudah, Lebih Akurat.</h1>
      <p>Akses informasi pasang surut secara terintegrasi untuk mendukung kebutuhan monitoring dan analisis.</p>
    </div>

    <div class="card-slot">

      <?php if ($sukses): ?>
        <!-- ===== Tampilan setelah pendaftaran berhasil ===== -->
        <div class="auth-card success-state">
          <img src="assets/img/logo-bmkg.png" alt="" class="card-logo">
          <svg class="tick" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.2">
            <circle cx="24" cy="24" r="21"/>
            <path d="M15 24.5l6.5 6.5L33 19" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <h2>Sign Up Success</h2>
          <p>Pendaftaran berhasil dan akun Anda sudah terdaftar.</p>
          <a class="btn" href="login.php?daftar=sukses" style="display:inline-block;line-height:38px;text-decoration:none;">Login Now</a>
        </div>

      <?php else: ?>
        <!-- ===== Form pendaftaran ===== -->
        <div class="auth-card">
          <img src="assets/img/logo-bmkg.png" alt="" class="card-logo">
          <h2>Buat Akun Baru Admin BMKG</h2>
          <p class="card-sub">Monitoring Pasang Surut Air Laut<br>BMKG Tanjung Perak Surabaya</p>

          <form class="form" method="post" action="register.php" novalidate>

            <?php if ($error): ?>
              <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="field">
              <label for="nama">Nama Pegawai</label>
              <div class="control">
                <input type="text" id="nama" name="nama" placeholder="Masukkan Nama"
                       value="<?= htmlspecialchars($nama) ?>" autocomplete="name">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <circle cx="12" cy="8" r="3.8"/>
                  <path d="M4.5 20c1.4-4 4.2-6 7.5-6s6.1 2 7.5 6"/>
                </svg>
              </div>
            </div>

            <div class="field">
              <label for="email">Email</label>
              <div class="control">
                <input type="email" id="email" name="email" placeholder="Masukkan Email"
                       value="<?= htmlspecialchars($email) ?>" autocomplete="email">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <rect x="2.5" y="4.5" width="19" height="15" rx="2"/>
                  <path d="M3 6l9 7 9-7"/>
                </svg>
              </div>
            </div>

            <div class="field">
              <label for="password">Password</label>
              <div class="control">
                <input type="password" id="password" name="password" placeholder="Masukkan Password"
                       autocomplete="new-password">
                <button type="button" class="icon" data-toggle="password" aria-label="Tampilkan password">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="8" cy="15" r="4.5"/>
                    <path d="M11.2 11.8L20 3M17 6l2.5 2.5M14.5 8.5L17 11"/>
                  </svg>
                </button>
              </div>
            </div>

            <button type="submit" class="btn" style="margin-top:8px;">Mulai Bekerja!</button>
          </form>

          <p class="card-foot">Sudah Punya Akun? <a href="login.php">Masuk Disini</a></p>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<script src="assets/js/app.js"></script>
</body>
</html>
