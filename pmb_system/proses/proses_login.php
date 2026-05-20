<?php
// proses/proses_login.php
require_once '../config/helper.php';
require_once '../config/koneksi.php';

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validasi input tidak kosong
if (empty($username) || empty($password)) {
    setFlash('error', 'Username dan password wajib diisi.');
    header('Location: ../login.php');
    exit;
}

// Cari user di database (prepared statement = aman dari SQL injection)
$stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1");
$stmt->execute([$username]);
$user = $stmt->fetch();

// Verifikasi password dengan password_verify (cocok dengan password_hash)
if (!$user || !password_verify($password, $user['password'])) {
    setFlash('error', 'Username atau password salah.');
    header('Location: ../login.php');
    exit;
}

// Login berhasil — simpan ke session
session_regenerate_id(true); // cegah session fixation
$_SESSION['user_id']  = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role']     = $user['role'];

// Redirect sesuai role
if ($user['role'] === 'admin') {
    header('Location: ../dashboard_admin.php');
} else {
    header('Location: ../dashboard_mahasiswa.php');
}
exit;
