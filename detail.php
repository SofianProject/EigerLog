<?php
require 'sesi.php';
require 'koneksi.php';

wajibLogin();

// 1. Tangkap dan Validasi Parameter ID dari URL
// Pastikan input berupa integer untuk mencegah SQL Injection berbasis URL
$id = $_GET['id'] ?? null;

if (!filter_var($id, FILTER_VALIDATE_INT)) {
    die("Akses ditolak: ID tidak valid.");
}
$id = (int)$id;

try {
    // 2. Kueri Detail Data
    $sql = "SELECT e.*, p.nama AS nama_penginput 
            FROM ekspedisi e 
            JOIN pengguna p ON e.pengguna_id = p.id 
            WHERE e.id = ?";
            
    $stmt = $koneksi->prepare($sql);
    
    /**
     * KOMENTAR PENJELAS LOGIKA PREPARED STATEMENT DETAIL:
     * Mengikat nilai parameter $id dengan tipe 'i' (integer).
     * Jika attacker mencoba memasukkan query string tambahan pada URL,
     * MySQL akan membuangnya karena hanya mengenali satu nilai numerik murni.
     */
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $hasil = $stmt->get_result();

    if ($hasil->num_rows === 0) {
        die("Data logbook dengan ID tersebut tidak ditemukan.");
    }

    $data = $hasil->fetch_assoc();
    
} catch (Exception $e) {
    error_log($e->getMessage());
    die("Terjadi kesalahan sistem saat mengambil detail data.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Detail Ekspedisi - <?= e($data['lokasi_gunung']) ?></title>
</head>
<body>
    <h2>Detail Catatan Ekspedisi</h2>
    
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Lokasi / Nama Gunung</th>
            <!-- e() memblokir XSS -->
            <td><?= e($data['lokasi_gunung']) ?></td>
        </tr>
        <tr>
            <th>Ketinggian</th>
            <td><?= (int)$data['ketinggian_mdpl'] ?> MDPL</td>
        </tr>
        <tr>
            <th>Status Jalur Saat Ini</th>
            <td><b><?= e($data['status_jalur']) ?></b></td>
        </tr>
        <tr>
            <th>Catatan Khusus / Syarat Perizinan</th>
            <!-- Menggunakan nl2br agar format Enter/Baris Baru (newline) pada teks dari textarea tetap rapi -->
            <td><?= nl2br(e($data['catatan_khusus'])) ?></td>
        </tr>
        <tr>
            <th>Dicatat Oleh</th>
            <td><?= e($data['nama_penginput']) ?></td>
        </tr>
    </table>
    
    <br>
    <a href="daftar.php">Kembali ke Daftar</a>
</body>
</html>