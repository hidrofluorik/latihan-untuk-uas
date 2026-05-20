<?php
// proses/proses_upload.php
require_once '../config/helper.php';
require_once '../config/koneksi.php';
requireMahasiswa();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard_mahasiswa.php');
    exit;
}

$userId    = $_SESSION['user_id'];
$uploadDir = dirname(__DIR__) . '/uploads/'; // path absolut ke folder uploads/

// Pastikan folder uploads ada dan bisa ditulis
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Cek file ada di request
if (empty($_FILES['dokumen']) || $_FILES['dokumen']['error'] !== UPLOAD_ERR_OK) {
    setFlash('error', 'Gagal upload. Pastikan file dipilih dan tidak melebihi batas server.');
    header('Location: ../dashboard_mahasiswa.php');
    exit;
}

$file     = $_FILES['dokumen'];
$maxSize  = 2 * 1024 * 1024;  // 2 MB
$allowed  = ['pdf', 'jpg', 'jpeg', 'png'];

// Validasi ukuran
if ($file['size'] > $maxSize) {
    setFlash('error', 'Ukuran file terlalu besar. Maks 2MB.');
    header('Location: ../dashboard_mahasiswa.php');
    exit;
}

// Validasi ekstensi (gunakan pathinfo, bukan MIME type saja)
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
    setFlash('error', 'Tipe file tidak diizinkan. Gunakan PDF, JPG, atau PNG.');
    header('Location: ../dashboard_mahasiswa.php');
    exit;
}

// Buat nama file unik: userId_timestamp_random.ext
$newName  = $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$destPath = $uploadDir . $newName;

// Pindahkan file dari tmp ke folder uploads
if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    setFlash('error', 'Gagal menyimpan file. Cek permission folder uploads/.');
    header('Location: ../dashboard_mahasiswa.php');
    exit;
}

// Simpan path relatif ke database
$relPath = 'uploads/' . $newName;
$stmt = $pdo->prepare("INSERT INTO dokumen (id_user, file_path, status) VALUES (?, ?, 'menunggu')");
$stmt->execute([$userId, $relPath]);

setFlash('success', 'Dokumen berhasil diupload dan menunggu verifikasi admin.');
header('Location: ../dashboard_mahasiswa.php');
exit;
