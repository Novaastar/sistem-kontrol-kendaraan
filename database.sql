CREATE DATABASE IF NOT EXISTS bank_jatim_kendaraan
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bank_jatim_kendaraan;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','driver') NOT NULL,
    nama VARCHAR(150) NOT NULL,
    foto VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE kendaraan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    merk VARCHAR(100) NOT NULL,
    nomor_polisi VARCHAR(20) NOT NULL UNIQUE,
    tahun SMALLINT NULL,
    status ENUM('aktif','tidak aktif') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE drivers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    nama VARCHAR(150) NOT NULL,
    no_hp VARCHAR(25) NULL,
    status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_driver_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE penugasan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    kendaraan_id INT NOT NULL,
    driver_id INT NOT NULL,
    tujuan VARCHAR(255) NULL,
    status ENUM('ditugaskan','berjalan','selesai','dibatalkan') NOT NULL DEFAULT 'ditugaskan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_penugasan_kendaraan FOREIGN KEY (kendaraan_id) REFERENCES kendaraan(id) ON DELETE RESTRICT,
    CONSTRAINT fk_penugasan_driver FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE RESTRICT
);

CREATE TABLE perjalanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    penugasan_id INT NOT NULL UNIQUE,
    jam_ambil TIME NULL,
    km_awal INT NULL,
    jam_pulang TIME NULL,
    km_akhir INT NULL,
    keterangan TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_perjalanan_penugasan FOREIGN KEY (penugasan_id) REFERENCES penugasan(id) ON DELETE CASCADE
);

CREATE TABLE cek_satpam (
    id INT AUTO_INCREMENT PRIMARY KEY,
    perjalanan_id INT NOT NULL,
    satpam_ke TINYINT NOT NULL,
    waktu TIME NULL,
    status ENUM('belum','sudah') NOT NULL DEFAULT 'belum',
    keterangan VARCHAR(255) NULL,
    UNIQUE KEY uq_cek (perjalanan_id, satpam_ke),
    CONSTRAINT fk_cek_perjalanan FOREIGN KEY (perjalanan_id) REFERENCES perjalanan(id) ON DELETE CASCADE
);

CREATE TABLE api_tokens (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_token_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_kendaraan_plat ON kendaraan(nomor_polisi);
CREATE INDEX idx_penugasan_tanggal ON penugasan(tanggal);
CREATE INDEX idx_penugasan_status ON penugasan(status);
