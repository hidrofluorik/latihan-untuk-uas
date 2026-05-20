<?php
// register.php
require_once 'config/helper.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard_mahasiswa.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — Sistem PMB</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card" style="max-width:480px;">
        <div class="logo">
            <h1>🎓 SISTEM PMB</h1>
            <p>Buat Akun Pendaftar Baru</p>
        </div>

        <?= getFlash() ?>

        <form action="proses/proses_register.php" method="POST">
            <!-- Akun -->
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Minimal 4 karakter" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Minimal 6 karakter" required>
            </div>

            <hr style="margin:20px 0;border-color:#dce3ec;">
            <p style="font-size:.82rem;color:#7f8c8d;margin-bottom:14px;font-weight:600;">DATA DIRI</p>

            <!-- Biodata -->
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Nama sesuai KTP" required>
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" placeholder="Alamat lengkap"></textarea>
            </div>
            <div class="form-group">
                <label>Pilih Jurusan</label>
                <select name="jurusan" required>
                    <option value="">-- Pilih Jurusan --</option>
                    <option>Teknik Informatika</option>
                    <option>Sistem Informasi</option>
                    <option>Teknik Elektro</option>
                    <option>Manajemen</option>
                    <option>Akuntansi</option>
                    <option>Ilmu Komunikasi</option>
                </select>
            </div>

            <button type="submit" class="btn btn-accent btn-block" style="margin-top:8px;">
                Daftar Sekarang
            </button>
        </form>

        <p style="text-align:center;margin-top:16px;font-size:.88rem;color:#7f8c8d;">
            Sudah punya akun?
            <a href="login.php" style="color:#1a3a5c;font-weight:600;">Login di sini</a>
        </p>
    </div>
</div>
</body>
</html>
