<?php
// ============================================================
//  config/helper.php
//  Fungsi-fungsi pembantu yang dipakai di seluruh aplikasi
// ============================================================

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Guard: paksa login ──────────────────────────────────────
function requireLogin(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit;
    }
}

// ── Guard: hanya admin ─────────────────────────────────────
function requireAdmin(): void {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') {
        header('Location: ../dashboard_mahasiswa.php');
        exit;
    }
}

// ── Guard: hanya mahasiswa ─────────────────────────────────
function requireMahasiswa(): void {
    requireLogin();
    if ($_SESSION['role'] !== 'mahasiswa') {
        header('Location: ../dashboard_admin.php');
        exit;
    }
}

// ── Sanitasi output HTML ───────────────────────────────────
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// ── Badge status verifikasi ────────────────────────────────
function badgeStatus(string $status): string {
    $map = [
        'menunggu'     => ['bg:#f39c12', 'Menunggu'],
        'diterima'     => ['bg:#27ae60', 'Diterima'],
        'ditolak'      => ['bg:#e74c3c', 'Ditolak'],
        'diverifikasi' => ['bg:#2980b9', 'Diverifikasi'],
    ];
    // Ganti baris yang error dengan ini:
$data = $map[$status] ?? ['bg:#95a5a6', ucfirst($status)];
$style = $data[0];
$label = $data[1];
    return "<span style='background:" . $color . ";color:#fff;padding:3px 10px;border-radius:12px;font-size:.8rem;'>" . $label . "</span>";
}
// ── Flash message ──────────────────────────────────────────
function setFlash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash(): string {
    if (empty($_SESSION['flash'])) return '';
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    $color = $f['type'] === 'success' ? '#27ae60' : '#e74c3c';
    return "<div style='background:{$color};color:#fff;padding:12px 18px;border-radius:8px;margin-bottom:16px;'>" . e($f['msg']) . "</div>";
}
