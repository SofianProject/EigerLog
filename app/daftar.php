<?php
require 'sesi.php';
require 'koneksi.php';

wajibLogin();

$pesanSukses = $_SESSION['flash_sukses'] ?? '';
unset($_SESSION['flash_sukses']);

try {
    $sql = "SELECT e.id, e.kegiatan, e.lokasi_kegiatan, e.tanggal_kegiatan, e.jenis_kegiatan, p.nama AS nama_penginput 
            FROM ekspedisi e 
            JOIN pengguna p ON e.pengguna_id = p.id 
            ORDER BY e.tanggal_kegiatan DESC, e.id DESC";
            
    $stmt = $koneksi->prepare($sql);
    $stmt->execute();
    $hasil = $stmt->get_result();
    
} catch (Exception $e) {
    error_log($e->getMessage());
    die("Gagal memuat daftar kegiatan.");
}

// Cek apakah user memiliki hak pengurus untuk memunculkan tombol tambah
$isPengurus = in_array($_SESSION['jabatan'] ?? '', ['admin', 'Komandan', 'Dewan Kehormatan']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silabus Eiger Corps</title>
    <link rel="stylesheet" href="../css/daftar.css">
    <style>
        /* Modifikasi CSS kecil khusus untuk badge baru di halaman ini */
        .badge-resmi { background-color: #5cb85c; }
        .badge-non-resmi { background-color: #5bc0de; }
        .badge-penjelajahan { background-color: #f0ad4e; }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="user-info">
            Eiger Corps Log | Halo, <b><?= e($_SESSION['nama']) ?></b> (<?= e($_SESSION['jabatan']) ?>)
        </div>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>

    <div class="container">
        <h2 class="header-title">Silabus Kegiatan Organisasi</h2>

        <div class="action-bar">
            <?php if ($isPengurus): ?>
                <a href="tambah.php" class="btn-tambah">+ Tambah Kegiatan</a>
            <?php else: ?>
                <span></span> 
            <?php endif; ?>
        </div>

        <?php if ($pesanSukses): ?>
            <div class="alert-success"><?= e($pesanSukses) ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kegiatan</th>
                    <th>Tanggal & Lokasi</th>
                    <th>Jenis</th>
                    <th>Dicatat Oleh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($baris = $hasil->fetch_assoc()): 
                    $jenisClass = '';
                    if ($baris['jenis_kegiatan'] === 'Resmi') $jenisClass = 'badge-resmi';
                    elseif ($baris['jenis_kegiatan'] === 'Non Resmi') $jenisClass = 'badge-non-resmi';
                    elseif ($baris['jenis_kegiatan'] === 'Penjelajahan') $jenisClass = 'badge-penjelajahan';
                    
                    // Format tanggal agar lebih rapi (Y-m-d -> d-m-Y)
                    $tgl = date("d/m/Y", strtotime($baris['tanggal_kegiatan']));
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><b><?= e($baris['kegiatan']) ?></b></td>
                        <td><?= $tgl ?> <br> <small style="color: #666;"><?= e($baris['lokasi_kegiatan']) ?></small></td>
                        <td><span class="badge <?= $jenisClass ?>"><?= e($baris['jenis_kegiatan']) ?></span></td>
                        <td><?= e($baris['nama_penginput']) ?></td>
                        <td><a href="detail.php?id=<?= (int)$baris['id'] ?>" class="btn-detail">Lihat Detail</a></td>
                    </tr>
                <?php endwhile; ?>
                
                <?php if ($hasil->num_rows === 0): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; color:#777;">Belum ada catatan kegiatan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>