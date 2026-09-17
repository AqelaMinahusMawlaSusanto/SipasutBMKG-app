<?php
session_start();
require __DIR__ . '/config/koneksi.php';

// Sudah login? langsung ke dashboard
if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$email = $_COOKIE['ingat_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $ingat    = isset($_POST['ingat']);

    if ($email === '' || $password === '') {
        $error = 'Email dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM admin WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama'];

            if ($ingat) {
                setcookie('ingat_email', $email, time() + 60 * 60 * 24 * 30, '/');
            } else {
                setcookie('ingat_email', '', time() - 3600, '/');
            }

            header('Location: dashboard.php');
            exit;
        }
        $error = 'Email atau password salah. Silakan coba lagi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Admin BMKG — Monitoring Pasang Surut</title>
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
      <div class="auth-card">
        <img src="assets/img/logo-bmkg.png" alt="" class="card-logo">
        <h2>Login Admin BMKG</h2>
        <p class="card-sub">Monitoring Pasang Surut Air Laut<br>BMKG Tanjung Perak Surabaya</p>

        <form class="form" method="post" action="login.php" novalidate>

          <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <?php if (isset($_GET['daftar']) && $_GET['daftar'] === 'sukses'): ?>
            <div class="alert alert-success">Akun berhasil dibuat. Silakan masuk.</div>
          <?php endif; ?>

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
                     autocomplete="current-password">
              <button type="button" class="icon" data-toggle="password" aria-label="Tampilkan password">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <circle cx="8" cy="15" r="4.5"/>
                  <path d="M11.2 11.8L20 3M17 6l2.5 2.5M14.5 8.5L17 11"/>
                </svg>
              </button>
            </div>
          </div>

          <label class="remember">
            <input type="checkbox" name="ingat" <?= $email ? 'checked' : '' ?>>
            Ingat Saya
          </label>

          <button type="submit" class="btn">Login</button>
        </form>

        <p class="card-foot">Belum Punya Akun? <a href="register.php">Klik Disini</a></p>
      </div>
    </div>

  </div>
</div>

<script src="assets/js/app.js"></script>
</body>
</html>
