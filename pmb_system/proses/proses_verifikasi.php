<?php
// proses/proses_verifikasi.php
require_once '../config/helper.php';
require_once '../config/koneksi.php';
requireAdmin();  // hanya admin yang boleh akses

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard_admin.php');
    exit;
}

$aksi   = $_POST['aksi']    ?? '';
$idUser = (int)($_POST['id_user'] ?? 0);

if (!$idUser) {
    setFlash('error', 'ID user tidak valid.');
    header('Location: ../dashboard_admin.php');
    exit;
}

switch ($aksi) {

    // ── Terima pendaftar ────────────────────────────────────
    case 'terima':
        $stmt = $pdo->prepare("
            UPDATE biodata
            SET status_verifikasi = 'diterima'
            WHERE id_user = ?
        ");
        $stmt->execute([$idUser]);

        // Sekaligus update semua dokumen user ini jadi diverifikasi
        $pdo->prepare("
            UPDATE dokumen SET status = 'diverifikasi' WHERE id_user = ?
        ")->execute([$idUser]);

        setFlash('success', 'Pendaftar berhasil diterima.');
        break;

    // ── Tolak pendaftar ─────────────────────────────────────
    case 'tolak':
        $stmt = $pdo->prepare("
            UPDATE biodata
            SET status_verifikasi = 'ditolak'
            WHERE id_user = ?
        ");
        $stmt->execute([$idUser]);

        $pdo->prepare("
            UPDATE dokumen SET status = 'ditolak' WHERE id_user = ?
        ")->execute([$idUser]);

        setFlash('success', 'Pendaftar telah ditolak.');
        break;

    // ── Simpan/update skor ujian ────────────────────────────
    case 'skor':
        $skor = (float)($_POST['skor'] ?? 0);
        $skor = max(0, min(100, $skor)); // clamp antara 0–100

        // INSERT jika belum ada, UPDATE jika sudah ada
        $stmt = $pdo->prepare("
            INSERT INTO nilai_ujian (id_user, skor)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE skor = VALUES(skor)
        ");
        $stmt->execute([$idUser, $skor]);

        setFlash('success', 'Skor ujian berhasil disimpan.');
        break;

    default:
        setFlash('error', 'Aksi tidak dikenal.');
}

header('Location: ../dashboard_admin.php');
exit;
