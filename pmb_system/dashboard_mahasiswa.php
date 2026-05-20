<?php
// dashboard_mahasiswa.php
require_once 'config/helper.php';
require_once 'config/koneksi.php';
requireMahasiswa();

$userId = $_SESSION['user_id'];

// Ambil data biodata
$stmtBio = $pdo->prepare("SELECT * FROM biodata WHERE id_user = ?");
$stmtBio->execute([$userId]);
$biodata = $stmtBio->fetch();

// Ambil dokumen (semua upload user ini)
$stmtDok = $pdo->prepare("SELECT * FROM dokumen WHERE id_user = ? ORDER BY uploaded_at DESC");
$stmtDok->execute([$userId]);
$dokumen = $stmtDok->fetchAll();

// Ambil nilai ujian
$stmtNilai = $pdo->prepare("SELECT skor FROM nilai_ujian WHERE id_user = ?");
$stmtNilai->execute([$userId]);
$nilai = $stmtNilai->fetchColumn();

// Map badge
$badgeMap = [
    'menunggu' => ['badge-warning', 'Menunggu Verifikasi'],
    'diterima' => ['badge-success', 'Diterima ✓'],
    'ditolak'  => ['badge-danger',  'Ditolak'],
];

$statusBio = $biodata['status_verifikasi'] ?? 'menunggu';
[$badgeClass, $badgeLabel] = $badgeMap[$statusBio] ?? ['badge-warning', 'Menunggu'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa — PMB</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="dash-wrapper">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <h2>🎓 PMB System</h2>
            <small>Portal Pendaftar</small>
        </div>
        <nav>
            <a href="dashboard_mahasiswa.php" class="active">📋 &nbsp;Dashboard</a>
            <a href="proses/logout.php">🚪 &nbsp;Logout</a>
        </nav>
        <div class="sidebar-footer">
            Login sebagai: <strong><?= e($_SESSION['username']) ?></strong>
        </div>
    </aside>

    <!-- Main -->
    <div class="main-content">
        <div class="topbar">
            <h1>Dashboard Pendaftaran</h1>
            <div class="user-info">Halo, <strong><?= e($_SESSION['username']) ?></strong>!</div>
        </div>

        <div class="content-area">
            <?= getFlash() ?>

            <!-- Status Card -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-num">
                        <span class="badge <?= $badgeClass ?>" style="font-size:.9rem;">
                            <?= $badgeLabel ?>
                        </span>
                    </div>
                    <div class="stat-label">Status Pendaftaran</div>
                </div>
                <div class="stat-card" style="border-left-color:#27ae60;">
                    <div class="stat-num"><?= count($dokumen) ?></div>
                    <div class="stat-label">Dokumen Diupload</div>
                </div>
                <div class="stat-card" style="border-left-color:#2980b9;">
                    <div class="stat-num"><?= $nilai !== false ? number_format($nilai, 1) : '—' ?></div>
                    <div class="stat-label">Skor Ujian</div>
                </div>
            </div>

            <!-- Biodata -->
            <div class="card">
                <h3>📝 Data Diri</h3>
                <?php if ($biodata): ?>
                <table>
                    <tr>
                        <td style="color:#7f8c8d;width:140px;">Nama</td>
                        <td><strong><?= e($biodata['nama']) ?></strong></td>
                    </tr>
                    <tr>
                        <td style="color:#7f8c8d;">Jurusan</td>
                        <td><?= e($biodata['jurusan']) ?></td>
                    </tr>
                    <tr>
                        <td style="color:#7f8c8d;">Alamat</td>
                        <td><?= e($biodata['alamat']) ?></td>
                    </tr>
                    <tr>
                        <td style="color:#7f8c8d;">Status</td>
                        <td><span class="badge <?= $badgeClass ?>"><?= $badgeLabel ?></span></td>
                    </tr>
                </table>
                <?php else: ?>
                <div class="alert alert-info">Data biodata belum tersedia. Silakan daftar ulang.</div>
                <?php endif; ?>
            </div>

            <!-- Upload Dokumen -->
            <div class="card">
                <h3>📁 Upload Dokumen</h3>
                <p style="font-size:.85rem;color:#7f8c8d;margin-bottom:16px;">
                    Upload dokumen pendaftaran (PDF/JPG/PNG, maks 2MB).
                    Dokumen yang diupload akan diverifikasi oleh admin.
                </p>

                <form action="proses/proses_upload.php" method="POST" enctype="multipart/form-data">
                    <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
                        <div class="form-group" style="flex:1;margin:0;">
                            <label>Pilih File</label>
                            <input type="file" name="dokumen" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>

                <!-- Riwayat Upload -->
                <?php if ($dokumen): ?>
                <div class="table-wrap" style="margin-top:20px;">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama File</th>
                                <th>Tanggal Upload</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($dokumen as $i => $dok): ?>
                        <?php
                            $dBadge = match($dok['status']) {
                                'diverifikasi' => 'badge-info',
                                'ditolak'      => 'badge-danger',
                                default        => 'badge-warning',
                            };
                        ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e(basename($dok['file_path'])) ?></td>
                            <td><?= e($dok['uploaded_at']) ?></td>
                            <td><span class="badge <?= $dBadge ?>"><?= ucfirst(e($dok['status'])) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p style="margin-top:14px;color:#aaa;font-size:.85rem;">Belum ada dokumen diupload.</p>
                <?php endif; ?>
            </div>

        </div><!-- /content-area -->
    </div><!-- /main-content -->
</div>
</body>
</html>
