<?php
require 'sesi.php';
require 'koneksi.php';

wajibLogin();

// memastikan input berupa integer untuk mencegah SQL Injection
$id = $_GET['id'] ?? null;
if (!filter_var($id, FILTER_VALIDATE_INT)) {
    die("Akses ditolak: ID tidak valid.");
}
$id = (int)$id;

try {
    $sql = "SELECT e.*, p.nama AS nama_penginput 
            FROM ekspedisi e 
            JOIN pengguna p ON e.pengguna_id = p.id 
            WHERE e.id = ?";
            
    //Memngikat nilai parameter $id dengan tipe integer
    //Jika attacker mencoba memasukkan query string tambahan pada URL
    //MySQL akan membuangnya karena hanya mengenali satu nilai numerik murni.
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $hasil = $stmt->get_result();

    if ($hasil->num_rows === 0) {
        die("Data kegiatan dengan ID tersebut tidak ditemukan.");
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kegiatan - <?= e($data['kegiatan']) ?></title>
    <link rel="stylesheet" href="../css/detail.css">
    <style>
        .badge-resmi { background-color: #5cb85c; }
        .badge-non-resmi { background-color: #5bc0de; }
        .badge-penjelajahan { background-color: #f0ad4e; }
    </style>
</head>
<body>

    <div class="detail-container">
        <div class="header-section">
            <h2>Laporan Detail Kegiatan</h2>
            <div class="subtitle">Eiger Corps - Repositori Logbook</div>
        </div>

        <table class="table-detail">
            <tr>
                <th>Nama Kegiatan</th>
                <td><b><?= e($data['kegiatan']) ?></b></td>
            </tr>
            <tr>
                <th>Lokasi / Titik Kumpul</th>
                <td><?= e($data['lokasi_kegiatan']) ?></td>
            </tr>
            <tr>
                <th>Tanggal Pelaksanaan</th>
                <td><?= date("d F Y", strtotime($data['tanggal_kegiatan'])) ?></td>
            </tr>
            <tr>
                <th>Jenis Kegiatan</th>
                <td>
                    <?php 
                        $jenisClass = '';
                        if ($data['jenis_kegiatan'] === 'Resmi') $jenisClass = 'badge-resmi';
                        elseif ($data['jenis_kegiatan'] === 'Non Resmi') $jenisClass = 'badge-non-resmi';
                        elseif ($data['jenis_kegiatan'] === 'Penjelajahan') $jenisClass = 'badge-penjelajahan';
                    ?>
                    <span class="badge <?= $jenisClass ?>"><?= e($data['jenis_kegiatan']) ?></span>
                </td>
            </tr>
            <tr>
                <th>Catatan Khusus / Perlengkapan</th>
                <td>
                    <?php if (empty($data['catatan_khusus'])): ?>
                        <span style="color: #999; font-style: italic;">Tidak ada catatan khusus.</span>
                    <?php else: ?>
                        <div class="note-box"><?= nl2br(e($data['catatan_khusus'])) ?></div>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Dicatat Oleh (Pengurus)</th>
                <td><?= e($data['nama_penginput']) ?></td>
            </tr>
        </table>
        
        <a href="daftar.php" class="btn-back">← Kembali ke Silabus</a>
    </div>

</body>
</html>