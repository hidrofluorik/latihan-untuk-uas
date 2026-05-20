<?php
// login.php
require_once 'config/helper.php';

// Jika sudah login, redirect ke dashboard sesuai role
if (!empty($_SESSION['user_id'])) {
    $redirect = $_SESSION['role'] === 'admin' ? 'dashboard_admin.php' : 'dashboard_mahasiswa.php';
    header("Location: $redirect");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem PMB</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="logo">
            <h1>🎓 SISTEM PMB</h1>
            <p>Penerimaan Mahasiswa Baru</p>
        </div>

        <?= getFlash() ?>

        <form action="proses/proses_login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       placeholder="Masukkan username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Masukkan password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">
                Masuk
            </button>
        </form>

        <p style="text-align:center;margin-top:20px;font-size:.88rem;color:#7f8c8d;">
            Belum punya akun?
            <a href="register.php" style="color:#1a3a5c;font-weight:600;">Daftar di sini</a>
        </p>
    </div>
</div>
</body>
</html>
