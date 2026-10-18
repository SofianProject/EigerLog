-- Hapus database jika sudah ada agar bersih saat diimpor ulang
DROP DATABASE IF EXISTS eigerlog_db;
CREATE DATABASE eigerlog_db;
USE eigerlog_db;

-- 1. Tabel Pengguna (menyimpan data autentikasi dan peran)
CREATE TABLE pengguna (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    peran ENUM('admin','anggota') NOT NULL DEFAULT 'anggota',
    hash_sandi VARCHAR(255) NOT NULL
);

-- 2. Tabel Entitas (menyimpan logbook ekspedisi, berelasi dengan pengguna)
CREATE TABLE ekspedisi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pengguna_id INT UNSIGNED NOT NULL,
    lokasi_gunung VARCHAR(150) NOT NULL,
    status_jalur ENUM('Resmi', 'Tutup', 'Buka Bersyarat') NOT NULL,
    ketinggian_mdpl INT NOT NULL,
    catatan_khusus TEXT,
    FOREIGN KEY (pengguna_id) REFERENCES pengguna (id) ON DELETE CASCADE
);

-- 3. Data Awal: Pengguna
-- Hash di bawah adalah hasil dari password_hash('sandi123', PASSWORD_DEFAULT)
INSERT INTO pengguna (username, nama, peran, hash_sandi) VALUES
('admin_sofian', 'Sofian', 'admin', '$2a$12$uc0/jbnGg290bX5V54hbGuCCAEH6dRoB7vSdsp.jwTYsNz.FLil0q'),
('anggota_eiger', 'Anggota Eiger Corps', 'anggota', '$2a$12$/YnLWZzN3uEPbtXGkkVlAuahjRfAtKhNLz.8TntAD3jVaaJigNR4C');

-- 4. Data Awal: Ekspedisi (5 Baris)
-- Pengguna ID 1 (Sofian - Admin) yang menginput semua data awal ini
INSERT INTO ekspedisi (pengguna_id, lokasi_gunung, status_jalur, ketinggian_mdpl, catatan_khusus) VALUES
(1, 'Gunung Slamet via Bambangan', 'Resmi', 3432, 'Cuaca cerah, sumber air di Pos 5 melimpah.'),
(1, 'Gunung Sindoro via Kledung', 'Tutup', 3136, 'Jalur ditutup sementara untuk pemulihan ekosistem pasca kemarau.'),
(1, 'Gunung Rinjani via Sembalun', 'Buka Bersyarat', 3726, 'Wajib melampirkan surat keterangan sehat dan didampingi guide lokal resmi dari Taman Nasional.'),
(1, 'Gunung Prau via Patakbanteng', 'Resmi', 2590, 'Kondisi jalur sangat padat di akhir pekan, waspada suhu udara drop di malam hari.'),
(1, 'Gunung Merapi via Selo', 'Tutup', 2930, 'Status waspada aktivitas vulkanik tinggi, radius 3 km dari puncak dilarang keras.');