DROP DATABASE IF EXISTS eigerlog_db;
CREATE DATABASE eigerlog_db;
USE eigerlog_db;

CREATE TABLE pengguna (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    jabatan ENUM('admin','Komandan','Wakil Komandan','Dewan Kehormatan','Panglima Divisi Gunung Hutan','Panglima Divisi Panjat Susur','Anggota Divisi Gunung Hutan', 'Anggota') NOT NULL DEFAULT 'Anggota',
    hash_sandi VARCHAR(255) NOT NULL
);

CREATE TABLE ekspedisi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pengguna_id INT UNSIGNED NOT NULL,
    kegiatan VARCHAR(150) NOT NULL,
    lokasi_kegiatan VARCHAR(150) NOT NULL,
    tanggal_kegiatan DATE NOT NULL DEFAULT CURRENT_DATE,
    jenis_kegiatan ENUM('Resmi', 'Non Resmi', 'Penjelajahan') NOT NULL,
    catatan_khusus TEXT,
    FOREIGN KEY (pengguna_id) REFERENCES pengguna (id) ON DELETE CASCADE
);

INSERT INTO pengguna (username, nama, jabatan, hash_sandi) VALUES
('admin', 'admin', 'admin', '$2a$12$1Ay8rjOdaJRH2H0MrJj6aut1H7U33p58YK2SSzwzguwKGJfWWcSky'),
('dk', 'Sofian', 'Dewan Kehormatan', '$2a$12$1Ay8rjOdaJRH2H0MrJj6aut1H7U33p58YK2SSzwzguwKGJfWWcSky'),
('bph1', 'Farrel', 'Komandan', '$2a$12$1Ay8rjOdaJRH2H0MrJj6aut1H7U33p58YK2SSzwzguwKGJfWWcSky'),
('bph2', 'Erlan', 'Wakil Komandan', '$2a$12$1Ay8rjOdaJRH2H0MrJj6aut1H7U33p58YK2SSzwzguwKGJfWWcSky'),
('panglima1', 'Zacky', 'Panglima Divisi Gunung Hutan', '$2a$12$1Ay8rjOdaJRH2H0MrJj6aut1H7U33p58YK2SSzwzguwKGJfWWcSky'),
('panglima2', 'Agha', 'Panglima Divisi Panjat Susur', '$2a$12$1Ay8rjOdaJRH2H0MrJj6aut1H7U33p58YK2SSzwzguwKGJfWWcSky'),
('madya1', 'Faisal', 'Anggota Divisi Gunung Hutan', '$2a$12$1Ay8rjOdaJRH2H0MrJj6aut1H7U33p58YK2SSzwzguwKGJfWWcSky');

INSERT INTO ekspedisi (pengguna_id, kegiatan, lokasi_kegiatan, tanggal_kegiatan, jenis_kegiatan, catatan_khusus) VALUES
(5, 'Pendidikan Dasar (DIKSAR)', 'Kalipagu', '2024-4-20', 'Resmi', 'Evaluasi mental dan fisik calon anggota berjalan lancar.'),
(6, 'Eksplorasi Susur Gua (Caving)', 'Gua Petruk', '2025-12-05', 'Penjelajahan', 'Main main'),
(3, 'Pendakian Gunung Sumbing', 'Gunung Sumbing via Gajah Mungkur', '2025-11-20', 'Non Resmi', 'cihuyy');