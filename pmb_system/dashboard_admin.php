<?php
// dashboard_admin.php
require_once 'config/helper.php';
require_once 'config/koneksi.php';
requireAdmin();

// ── Statistik ────────────────────────────────────────────
$stats = $pdo->query("
    SELECT
        COUNT(*) AS total,
        SUM(status_verifikasi = 'menunggu') AS menunggu,
        SUM(status_verifikasi = 'diterima') AS diterima,
        SUM(status_verifikasi = 'ditolak')  AS ditolak
    FROM biodata
")->fetch();

// ── Daftar Pendaftar ──────────────────────────────────────
$pendaftar = $pdo->query("
    SELECT
        u.id, u.username,
        b.nama, b.jurusan, b.alamat, b.status_verifikasi,
        n.skor,
        (SELECT COUNT(*) FROM dokumen d WHERE d.id_user = u.id) AS jml_dok
    FROM users u
    LEFT JOIN biodata b    ON b.id_user = u.id
    LEFT JOIN nilai_ujian n ON n.id_user = u.id
    WHERE u.role = 'mahasiswa'
    ORDER BY u.id DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — PMB</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .action-btns { display:flex; gap:6px; flex-wrap:wrap; }
        .skor-input  { width:70px; padding:5px 8px; border-radius:6px; border:1.5px solid #dce3ec; font-size:.85rem; }
    </style>
</head>
<body>
<div class="dash-wrapper">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <h2>🎓 PMB Admin</h2>
            <small>Panel Administrasi</small>
        </div>
        <nav>
            <a href="dashboard_admin.php" class="active">📊 &nbsp;Dashboard</a>
            <a href="proses/logout.php">🚪 &nbsp;Logout</a>
        </nav>
        <div class="sidebar-footer">
            Admin: <strong><?= e($_SESSION['username']) ?></strong>
        </div>
    </aside>

    <!-- Main -->
    <div class="main-content">
        <div class="topbar">
            <h1>Manajemen Pendaftar</h1>
            <div class="user-info">Role: <strong>Administrator</strong></div>
        </div>

        <div class="content-area">
            <?= getFlash() ?>

            <!-- Statistik -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-num"><?= $stats['total'] ?></div>
                    <div class="stat-label">Total Pendaftar</div>
                </div>
                <div class="stat-card" style="border-left-color:#f39c12;">
                    <div class="stat-num"><?= $stats['menunggu'] ?></div>
                    <div class="stat-label">Menunggu Verifikasi</div>
                </div>
                <div class="stat-card" style="border-left-color:#27ae60;">
                    <div class="stat-num"><?= $stats['diterima'] ?></div>
                    <div class="stat-label">Diterima</div>
                </div>
                <div class="stat-card" style="border-left-color:#e74c3c;">
                    <div class="stat-num"><?= $stats['ditolak'] ?></div>
                    <div class="stat-label">Ditolak</div>
                </div>
            </div>

            <!-- Tabel Pendaftar -->
            <div class="card">
                <h3>📋 Daftar Pendaftar</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Username</th>
                                <th>Nama</th>
                                <th>Jurusan</th>
                                <th>Dok</th>
                                <th>Skor</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($pendaftar)): ?>
                            <tr><td colspan="8" style="text-align:center;color:#aaa;padding:24px;">
                                Belum ada pendaftar.
                            </td></tr>
                        <?php else: ?>
                        <?php foreach ($pendaftar as $i => $p): ?>
                        <?php
                            $bMap = [
                                'menunggu' => 'badge-warning',
                                'diterima' => 'badge-success',
                                'ditolak'  => 'badge-danger',
                            ];
                            $bClass = $bMap[$p['status_verifikasi'] ?? 'menunggu'] ?? 'badge-warning';
                        ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e($p['username']) ?></td>
                            <td><?= e($p['nama'] ?? '—') ?></td>
                            <td><?= e($p['jurusan'] ?? '—') ?></td>
                            <td style="text-align:center;"><?= $p['jml_dok'] ?></td>

                            <!-- Kolom input skor -->
                            <td>
                                <form action="proses/proses_verifikasi.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="aksi"    value="skor">
                                    <input type="hidden" name="id_user" value="<?= $p['id'] ?>">
                                    <input type="number" name="skor" class="skor-input"
                                           value="<?= e($p['skor'] ?? '') ?>"
                                           min="0" max="100" step="0.01" placeholder="0">
                                    <button class="btn btn-sm btn-primary" style="margin-top:4px;" title="Simpan Skor">💾</button>
                                </form>
                            </td>

                            <td>
                                <span class="badge <?= $bClass ?>">
                                    <?= ucfirst(e($p['status_verifikasi'] ?? 'menunggu')) ?>
                                </span>
                            </td>

                            <!-- Tombol Aksi -->
                            <td>
                                <div class="action-btns">
                                    <!-- Terima -->
                                    <form action="proses/proses_verifikasi.php" method="POST">
                                        <input type="hidden" name="aksi"    value="terima">
                                        <input type="hidden" name="id_user" value="<?= $p['id'] ?>">
                                        <button class="btn btn-sm btn-success" title="Terima Pendaftar">✓ Terima</button>
                                    </form>

                                    <!-- Tolak -->
                                    <form action="proses/proses_verifikasi.php" method="POST"
                                          onsubmit="return confirm('Yakin menolak pendaftar ini?')">
                                        <input type="hidden" name="aksi"    value="tolak">
                                        <input type="hidden" name="id_user" value="<?= $p['id'] ?>">
                                        <button class="btn btn-sm btn-danger" title="Tolak Pendaftar">✗ Tolak</button>
                                    </form>

                                    <!-- Lihat Dokumen -->
                                    <a href="proses/lihat_dokumen.php?id_user=<?= $p['id'] ?>"
                                       class="btn btn-sm btn-accent">📁 Dok</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div><!-- /card -->

        </div><!-- /content-area -->
    </div><!-- /main-content -->
</div>
</body>
</html>
