<?php
// proses/lihat_dokumen.php  — hanya admin
require_once '../config/helper.php';
require_once '../config/koneksi.php';
requireAdmin();

$idUser = (int)($_GET['id_user'] ?? 0);

// Ambil info user
$user = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$user->execute([$idUser]);
$user = $user->fetch();

if (!$user) {
    setFlash('error', 'User tidak ditemukan.');
    header('Location: ../dashboard_admin.php'); exit;
}

// Ambil semua dokumen user
$docs = $pdo->prepare("SELECT * FROM dokumen WHERE id_user = ? ORDER BY uploaded_at DESC");
$docs->execute([$idUser]);
$docs = $docs->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dokumen — <?= e($user['username']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="dash-wrapper">
    <aside class="sidebar">
        <div class="sidebar-brand"><h2>🎓 PMB Admin</h2></div>
        <nav>
            <a href="../dashboard_admin.php">← Kembali</a>
            <a href="logout.php">🚪 Logout</a>
        </nav>
    </aside>
    <div class="main-content">
        <div class="topbar">
            <h1>Dokumen Pendaftar: <?= e($user['username']) ?></h1>
        </div>
        <div class="content-area">
            <?= getFlash() ?>
            <div class="card">
                <h3>📁 Daftar Dokumen</h3>
                <?php if (empty($docs)): ?>
                    <div class="alert alert-info">Pendaftar belum mengupload dokumen.</div>
                <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr><th>#</th><th>File</th><th>Waktu Upload</th><th>Status</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($docs as $i => $d): ?>
                        <?php
                            $bc = match($d['status']) {
                                'diverifikasi' => 'badge-info',
                                'ditolak'      => 'badge-danger',
                                default        => 'badge-warning'
                            };
                        ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><?= e(basename($d['file_path'])) ?></td>
                            <td><?= e($d['uploaded_at']) ?></td>
                            <td><span class="badge <?= $bc ?>"><?= ucfirst(e($d['status'])) ?></span></td>
                            <td>
                                <a href="../<?= e($d['file_path']) ?>" target="_blank"
                                   class="btn btn-sm btn-primary">👁 Lihat</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
