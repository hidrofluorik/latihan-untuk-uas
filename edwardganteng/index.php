<?php
session_start();
require 'koneksi.php';

// Cek apakah sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// --- FITUR UPLOAD DOKUMEN (STUDENT) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['dokumen']) && $_SESSION['role'] === 'student') {
    $target_dir = "uploads/";
    // Buat folder uploads otomatis jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Format nama file agar unik (waktu + nama asli)
    $file_name = time() . "_" . basename($_FILES["dokumen"]["name"]);
    $target_file = $target_dir . $file_name;
    
    if (move_uploaded_file($_FILES["dokumen"]["tmp_name"], $target_file)) {
        $stmt = $pdo->prepare("UPDATE users SET dokumen = ? WHERE id = ?");
        $stmt->execute([$file_name, $_SESSION['user_id']]);
        $msg_success = "Dokumen berhasil diunggah dan sedang diproses!";
    } else {
        $msg_error = "Gagal mengunggah dokumen. Pastikan folder memiliki izin tulis.";
    }
}

// --- FITUR TERIMA/TOLAK (ADMIN) ---
if (isset($_GET['action']) && isset($_GET['id']) && $_SESSION['role'] === 'admin') {
    $action = $_GET['action'];
    $user_id = $_GET['id'];
    
    if ($action === 'terima') {
        $pdo->prepare("UPDATE users SET status_penerimaan = 'diterima' WHERE id = ?")->execute([$user_id]);
    } elseif ($action === 'tolak') {
        $pdo->prepare("UPDATE users SET status_penerimaan = 'ditolak' WHERE id = ?")->execute([$user_id]);
    }
    // Redirect agar tidak tersubmit ulang saat di-refresh
    header("Location: index.php?page=admin");
    exit;
}

// Ambil data user saat ini untuk dicek status penerimaannya
$stmt_user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->execute([$_SESSION['user_id']]);
$current_user = $stmt_user->fetch();

// Ambil data pengumuman dan ujian
$pengumuman = $pdo->query("SELECT * FROM pengumuman ORDER BY tanggal DESC")->fetchAll();
$ujian = $pdo->query("SELECT * FROM ujian ORDER BY tanggal_ujian ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal PMB - Dashboard Calon Mahasiswa</title>
    <style>
        :root {
            --primary-color: #004b87; /* Biru Kampus */
            --secondary-color: #f1c40f; /* Kuning Aksen */
            --bg-color: #f4f7f6;
            --text-color: #333;
        }

        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: var(--bg-color); 
            color: var(--text-color); 
            margin: 0; 
            padding: 0;
        }

        /* Navbar Styling */
        .navbar { 
            background: var(--primary-color); 
            color: white; 
            padding: 15px 40px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-size: 20px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 15px;
        }

        .btn {
            color: white; 
            text-decoration: none; 
            padding: 8px 16px; 
            border-radius: 5px; 
            background: rgba(255,255,255,0.1);
            transition: all 0.3s;
        }

        .btn:hover { background: rgba(255,255,255,0.25); }
        
        .btn-admin { 
            background: #e74c3c; 
            font-weight: bold; 
        }
        .btn-admin:hover { background: #c0392b; }

        /* Container Layout */
        .container { 
            max-width: 1000px; 
            margin: 40px auto; 
            padding: 0 20px; 
        }

        .header-title {
            margin-bottom: 30px;
            color: var(--primary-color);
        }

        /* Card Component */
        .card { 
            background: white; 
            border-radius: 10px; 
            padding: 25px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            margin-bottom: 30px; 
        }

        .card-title { 
            border-bottom: 2px solid #eee; 
            padding-bottom: 15px; 
            margin-top: 0; 
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Pengumuman Styling */
        .announcement-item { 
            border-left: 5px solid var(--secondary-color); 
            background: #fafafa; 
            padding: 15px 20px; 
            margin-bottom: 15px; 
            border-radius: 0 8px 8px 0; 
        }
        .announcement-item h4 { margin: 0 0 5px 0; font-size: 18px; color: var(--primary-color); }
        .announcement-item .date { color: #888; font-size: 13px; margin-bottom: 8px; display: block; }
        .announcement-item p { margin: 0; line-height: 1.6; }

        /* Table Styling */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
            border-radius: 8px;
            overflow: hidden;
        }
        th, td { 
            padding: 15px; 
            text-align: left; 
            border-bottom: 1px solid #eee; 
        }
        th { 
            background-color: var(--primary-color); 
            color: white; 
            font-weight: 500;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: #f9f9f9; }
        .badge {
            background: #e8f4fd;
            color: var(--primary-color);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

    </style>
</head>
<body>

    <!-- NAVBAR -->
    <div class="navbar">
        <div class="navbar-brand">
            🎓 Portal PMB 2026
        </div>
        <div class="user-menu">
            <span>Selamat datang, <b><?= htmlspecialchars($_SESSION['username']) ?></b></span>
            
            <!-- MENU ADMIN JIKA ROLE ADALAH ADMIN -->
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="index.php?page=admin" class="btn btn-admin">⚙️ Panel Admin</a> 
            <?php endif; ?>
            
            <a href="logout.php" class="btn">Keluar</a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="container">
        
        <?php if (isset($_GET['page']) && $_GET['page'] === 'admin' && $_SESSION['role'] === 'admin'): ?>
            
            <!-- ============================== -->
            <!--       HALAMAN PANEL ADMIN      -->
            <!-- ============================== -->
            <div class="header-title">
                <h2>Panel Review Dokumen Calon Mahasiswa</h2>
                <p>Review dokumen yang diunggah dan tentukan kelulusan calon mahasiswa.</p>
                <a href="index.php" class="btn" style="background: var(--primary-color); display:inline-block; margin-top:10px;">⬅ Kembali ke Dashboard</a>
            </div>
            
            <div class="card">
                <h3 class="card-title">👥 Daftar Calon Mahasiswa</h3>
                <?php
                    // Ambil semua user dengan role student
                    $students = $pdo->query("SELECT * FROM users WHERE role = 'student'")->fetchAll();
                ?>
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Dokumen Persyaratan</th>
                                <th>Status Kelulusan</th>
                                <th>Aksi Review</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $s): ?>
                                <tr>
                                    <td><b><?= htmlspecialchars($s['username']) ?></b></td>
                                    <td>
                                        <?php if ($s['dokumen']): ?>
                                            <a href="uploads/<?= htmlspecialchars($s['dokumen']) ?>" target="_blank" style="color:var(--primary-color); font-weight:bold; text-decoration:none;">📄 Lihat File</a>
                                        <?php else: ?>
                                            <span style="color: #888;">Belum Upload</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                            if ($s['status_penerimaan'] == 'diterima') echo "<span class='badge' style='background:#d4edda; color:#155724'>Diterima</span>";
                                            elseif ($s['status_penerimaan'] == 'ditolak') echo "<span class='badge' style='background:#f8d7da; color:#721c24'>Ditolak</span>";
                                            else echo "<span class='badge' style='background:#fff3cd; color:#856404'>Pending</span>";
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($s['dokumen'] && $s['status_penerimaan'] == 'pending'): ?>
                                            <a href="index.php?action=terima&id=<?= $s['id'] ?>" class="btn" style="background: #28a745; font-size:12px; padding:6px 12px; margin-right:5px;">Terima</a>
                                            <a href="index.php?action=tolak&id=<?= $s['id'] ?>" class="btn" style="background: #dc3545; font-size:12px; padding:6px 12px;">Tolak</a>
                                        <?php else: ?>
                                            <span style="color:#aaa;">- Selesai -</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>
            
            <!-- ============================== -->
            <!--     HALAMAN DASHBOARD USER     -->
            <!-- ============================== -->
            <div class="header-title">
                <h2>Dashboard Calon Mahasiswa Baru</h2>
                <p>Pantau informasi terbaru dan jadwal seleksi ujian masuk Anda di sini.</p>
            </div>

            <!-- 1. ALERT JIKA DITERIMA -->
            <?php if ($current_user['status_penerimaan'] === 'diterima'): ?>
                <div class="card" style="background-color: #d4edda; border-left: 5px solid #28a745;">
                    <h3 style="color: #155724; margin-top:0; border-bottom:none;">🎉 Selamat! Anda Diterima</h3>
                    <p style="color: #155724; font-size:15px; margin-bottom: 15px;">Selamat <b><?= htmlspecialchars($_SESSION['username']) ?></b>, Anda telah dinyatakan <b>LULUS</b> seleksi penerimaan mahasiswa baru di kampus kami. Silakan lanjutkan ke tahap daftar ulang.</p>
                    <a href="#" class="btn" style="background: #28a745; color:white; font-weight:bold;">📋 Isi Formulir Daftar Ulang</a>
                </div>
            <?php endif; ?>

            <!-- 2. SECTION UPLOAD DOKUMEN -->
            <div class="card">
                <h3 class="card-title">📁 Upload Dokumen Persyaratan (KTP / Ijazah)</h3>
                
                <?php if (isset($msg_success)) echo "<p style='color: #155724; background: #d4edda; padding: 10px; border-radius: 5px; font-weight:bold;'>$msg_success</p>"; ?>
                <?php if (isset($msg_error)) echo "<p style='color: #721c24; background: #f8d7da; padding: 10px; border-radius: 5px; font-weight:bold;'>$msg_error</p>"; ?>
                
                <?php if ($current_user['dokumen']): ?>
                    <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; border: 1px solid #eee;">
                        <p style="margin-top:0;">✅ Anda sudah mengunggah dokumen persyaratan: <br><br><a href="uploads/<?= htmlspecialchars($current_user['dokumen']) ?>" target="_blank" style="color:var(--primary-color); font-weight:bold; text-decoration:none; background: #e8f4fd; padding: 8px 15px; border-radius: 5px;">📄 Lihat Dokumen Anda</a></p>
                        <p style="margin-bottom:0;">Status Review Admin: 
                            <?php 
                                if ($current_user['status_penerimaan'] == 'diterima') echo "<span class='badge' style='background:#d4edda; color:#155724'>Lulus / Diterima</span>";
                                elseif ($current_user['status_penerimaan'] == 'ditolak') echo "<span class='badge' style='background:#f8d7da; color:#721c24'>Tidak Lulus / Ditolak</span>";
                                else echo "<span class='badge' style='background:#fff3cd; color:#856404'>Sedang Menunggu Review</span>";
                            ?>
                        </p>
                    </div>
                <?php else: ?>
                    <p>Silakan unggah dokumen persyaratan Anda dalam satu file (PDF/JPG/PNG).</p>
                    <form method="POST" enctype="multipart/form-data" style="margin-top: 15px; background: #fafafa; padding: 20px; border-radius: 8px; border: 1px dashed #ccc;">
                        <input type="file" name="dokumen" required accept=".pdf,.jpg,.jpeg,.png" style="margin-bottom: 15px; display:block; width: 100%; box-sizing: border-box;">
                        <button type="submit" class="btn" style="background: var(--primary-color); font-weight:bold;">⬆ Unggah Dokumen</button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- SECTION PENGUMUMAN -->
            <div class="card">
                <h3 class="card-title">📢 Informasi & Pengumuman</h3>
                <div class="announcement-list">
                    <?php if (count($pengumuman) > 0): ?>
                        <?php foreach ($pengumuman as $p): ?>
                            <div class="announcement-item">
                                <h4><?= htmlspecialchars($p['judul']) ?></h4>
                                <span class="date">📅 Dipublikasikan pada: <?= date('d M Y', strtotime($p['tanggal'])) ?></span>
                                <p><?= nl2br(htmlspecialchars($p['isi'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #888;">Belum ada pengumuman saat ini.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- SECTION UJIAN -->
            <div class="card">
                <h3 class="card-title">📝 Jadwal Ujian Seleksi</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Mata Ujian / Tahapan</th>
                            <th>Tanggal Pelaksanaan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($ujian) > 0): ?>
                            <?php foreach ($ujian as $u): ?>
                                <tr>
                                    <td><b><?= htmlspecialchars($u['nama_ujian']) ?></b></td>
                                    <td><?= date('d F Y', strtotime($u['tanggal_ujian'])) ?></td>
                                    <td><span class="badge">Wajib Hadir</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: #888;">Jadwal ujian belum tersedia.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?> <!-- PENUTUP IF ADMIN PAGE / USER DASHBOARD -->

    </div>

</body>
</html>