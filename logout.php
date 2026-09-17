<?php
session_start();

// Hapus semua data session
$_SESSION = [];
session_destroy();

// Hapus cookie "ingat saya" juga kalau ada
if (isset($_COOKIE['ingat_email'])) {
    setcookie('ingat_email', '', time() - 3600, '/');
}

header('Location: login.php');
exit;