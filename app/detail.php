<?php
require 'sesi.php';
require 'koneksi.php';

wajibLogin();

// 1. Tangkap dan Validasi Parameter ID dari URL
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Ekspedisi - <?= e($data['lokasi_gunung']) ?></title>
    <link rel="stylesheet" href="../css/detail.css">
</head>
<body>

    <div class="detail-container">
        <div class="header-section">
            <h2>Laporan Detail Ekspedisi</h2>
            <div class="subtitle">Sistem Informasi Jejak Alam EigerLog</div>
        </div>

        <table class="table-detail">
            <tr>
                <th>Lokasi / Nama Gunung</th>
                <td><b><?= e($data['lokasi_gunung']) ?></b></td>
            </tr>
            <tr>
                <th>Ketinggian Puncak</th>
                <td><?= (int)$data['ketinggian_mdpl'] ?> MDPL</td>
            </tr>
            <tr>
                <th>Status Jalur Saat Ini</th>
                <td>
                    <?php 
                        $statusClass = '';
                        if ($data['status_jalur'] === 'Resmi') $statusClass = 'badge-resmi';
                        elseif ($data['status_jalur'] === 'Tutup') $statusClass = 'badge-tutup';
                        elseif ($data['status_jalur'] === 'Buka Bersyarat') $statusClass = 'badge-bersyarat';
                    ?>
                    <span class="badge <?= $statusClass ?>"><?= e($data['status_jalur']) ?></span>
                </td>
            </tr>
            <tr>
                <th>Catatan Khusus / Perizinan</th>
                <td>
                    <!-- Teks di dalam note-box tetap di-escape dan di-nl2br agar baris baru tidak hilang -->
                    <?php if (empty($data['catatan_khusus'])): ?>
                        <span style="color: #999; font-style: italic;">Tidak ada catatan khusus.</span>
                    <?php else: ?>
                        <div class="note-box"><?= nl2br(e($data['catatan_khusus'])) ?></div>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Dicatat Oleh (Admin)</th>
                <td><?= e($data['nama_penginput']) ?></td>
            </tr>
        </table>
        
        <a href="daftar.php" class="btn-back">← Kembali ke Daftar</a>
    </div>

</body>
</html>