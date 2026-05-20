<?php
// ============================================================
//  config/koneksi.php
//  Konfigurasi koneksi ke database MySQL
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // default XAMPP
define('DB_PASS', '');           // default XAMPP (kosong)
define('DB_NAME', 'db_pmb');
define('DB_PORT', 3306);

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // Tampilkan pesan ramah, jangan expose error ke user di production
    die('<div style="font-family:sans-serif;color:#c0392b;padding:20px;">
            <strong>Koneksi database gagal.</strong><br>
            Pastikan XAMPP MySQL sudah berjalan dan database <em>db_pmb</em> sudah dibuat.<br>
            <small>Error: ' . htmlspecialchars($e->getMessage()) . '</small>
         </div>');
}
