<?php
// proses/proses_register.php
require_once '../config/helper.php';
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$nama     = trim($_POST['nama']     ?? '');
$alamat   = trim($_POST['alamat']   ?? '');
$jurusan  = trim($_POST['jurusan']  ?? '');

// Validasi
if (strlen($username) < 4) {
    setFlash('error', 'Username minimal 4 karakter.');
    header('Location: ../register.php'); exit;
}
if (strlen($password) < 6) {
    setFlash('error', 'Password minimal 6 karakter.');
    header('Location: ../register.php'); exit;
}
if (empty($nama) || empty($jurusan)) {
    setFlash('error', 'Nama dan jurusan wajib diisi.');
    header('Location: ../register.php'); exit;
}

// Cek username sudah dipakai
$cek = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$cek->execute([$username]);
if ($cek->fetch()) {
    setFlash('error', 'Username sudah digunakan, pilih username lain.');
    header('Location: ../register.php'); exit;
}

// Simpan user baru (gunakan transaksi supaya data konsisten)
try {
    $pdo->beginTransaction();

    // Insert ke tabel users
    $hashPw = password_hash($password, PASSWORD_DEFAULT);
    $insUser = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'mahasiswa')");
    $insUser->execute([$username, $hashPw]);
    $userId = $pdo->lastInsertId();

    // Insert biodata
    $insBio = $pdo->prepare("
        INSERT INTO biodata (id_user, nama, alamat, jurusan, status_verifikasi)
        VALUES (?, ?, ?, ?, 'menunggu')
    ");
    $insBio->execute([$userId, $nama, $alamat, $jurusan]);

    $pdo->commit();

    setFlash('success', 'Registrasi berhasil! Silakan login.');
    header('Location: ../login.php');

} catch (Exception $e) {
    $pdo->rollBack();
    setFlash('error', 'Terjadi kesalahan, coba lagi.');
    header('Location: ../register.php');
}
exit;
