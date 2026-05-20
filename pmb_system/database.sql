-- ============================================================
--  SISTEM PMB (Penerimaan Mahasiswa Baru)
--  Jalankan file ini di phpMyAdmin atau MySQL CLI
-- ============================================================

CREATE DATABASE IF NOT EXISTS db_pmb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_pmb;

-- Tabel Users (akun login)
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,          -- disimpan hashed (password_hash)
    role        ENUM('mahasiswa','admin') NOT NULL DEFAULT 'mahasiswa',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Biodata Pendaftar
CREATE TABLE IF NOT EXISTS biodata (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    id_user         INT NOT NULL UNIQUE,
    nama            VARCHAR(100) NOT NULL,
    alamat          TEXT,
    jurusan         VARCHAR(100),
    status_verifikasi ENUM('menunggu','diterima','ditolak') DEFAULT 'menunggu',
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel Dokumen Upload
CREATE TABLE IF NOT EXISTS dokumen (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    id_user     INT NOT NULL,
    file_path   VARCHAR(255) NOT NULL,
    status      ENUM('menunggu','diverifikasi','ditolak') DEFAULT 'menunggu',
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel Nilai Ujian
CREATE TABLE IF NOT EXISTS nilai_ujian (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL UNIQUE,
    skor    DECIMAL(5,2) DEFAULT 0.00,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
--  Seed: Akun Admin Default
--  Username: admin | Password: admin123
-- ============================================================
INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Password di atas adalah hash dari "admin123"
-- Ganti dengan: echo password_hash('admin123', PASSWORD_DEFAULT);
