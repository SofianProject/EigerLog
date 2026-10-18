<?php
require 'sesi.php';
require 'koneksi.php';

wajibLogin();

$pesanSukses = $_SESSION['flash_sukses'] ?? '';
unset($_SESSION['flash_sukses']);

try {
    $sql = "SELECT e.id, e.lokasi_gunung, e.status_jalur, p.nama AS nama_penginput 
            FROM ekspedisi e 
            JOIN pengguna p ON e.pengguna_id = p.id 
            ORDER BY e.id DESC";
            
    $stmt = $koneksi->prepare($sql);
    $stmt->execute();
    $hasil = $stmt->get_result();
    
} catch (Exception $e) {
    error_log($e->getMessage());
    die("Gagal memuat daftar ekspedisi.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EigerLog</title>
    <link rel="stylesheet" href="../css/daftar.css">
</head>
<body>

    <!-- Navigasi Atas -->
    <div class="navbar">
        <div class="user-info">
            EigerLog System | Halo, <b><?= e($_SESSION['nama']) ?></b> (<?= ucfirst(e($_SESSION['peran'])) ?>)
        </div>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>

    <!-- Container Konten -->
    <div class="container">
        <h2 class="header-title">Logbook Ekspedisi Terkini</h2>

        <div class="action-bar">
            <!-- Menampilkan tombol tambah HANYA jika yang login adalah admin -->
            <?php if ($_SESSION['peran'] === 'admin'): ?>
                <a href="tambah.php" class="btn-tambah">+ Tambah Data Ekspedisi</a>
            <?php else: ?>
                <!-- Spacer jika bukan admin agar layout tidak berantakan -->
                <span></span> 
            <?php endif; ?>
        </div>

        <!-- Flash Message PRG -->
        <?php if ($pesanSukses): ?>
            <div class="alert-success"><?= e($pesanSukses) ?></div>
        <?php endif; ?>

        <!-- Tabel Data -->
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Lokasi Gunung</th>
                    <th>Status Jalur</th>
                    <th>Dicatat Oleh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($baris = $hasil->fetch_assoc()): 
                    // Menentukan kelas CSS badge berdasarkan isi kolom status_jalur
                    $statusClass = '';
                    if ($baris['status_jalur'] === 'Resmi') $statusClass = 'badge-resmi';
                    elseif ($baris['status_jalur'] === 'Tutup') $statusClass = 'badge-tutup';
                    elseif ($baris['status_jalur'] === 'Buka Bersyarat') $statusClass = 'badge-bersyarat';
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><b><?= e($baris['lokasi_gunung']) ?></b></td>
                        <td><span class="badge <?= $statusClass ?>"><?= e($baris['status_jalur']) ?></span></td>
                        <td><?= e($baris['nama_penginput']) ?></td>
                        <td><a href="detail.php?id=<?= (int)$baris['id'] ?>" class="btn-detail">Lihat Detail</a></td>
                    </tr>
                <?php endwhile; ?>
                
                <?php if ($hasil->num_rows === 0): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; color:#777;">Belum ada catatan ekspedisi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>