<?php
require 'sesi.php';
require 'koneksi.php';

// Pastikan pengguna sudah login
wajibLogin();

// Ambil pesan sukses dari halaman tambah (jika ada) dan hapus dari sesi
$pesanSukses = $_SESSION['flash_sukses'] ?? '';
unset($_SESSION['flash_sukses']);

try {
    /**
     * KOMENTAR PENJELAS LOGIKA KUERI READ & JOIN:
     * Kueri ini mengambil data dari tabel 'ekspedisi' sekaligus
     * mengambil 'nama' dari tabel 'pengguna' dengan mencocokkan pengguna_id.
     * Data diurutkan dari ID terbesar ke terkecil (terbaru).
     */
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
    <title>Daftar Logbook Ekspedisi</title>
</head>
<body>
    <h2>Daftar Logbook Ekspedisi</h2>
    
    <p>
        Selamat datang, <b><?= e($_SESSION['nama']) ?></b> (Peran: <?= e($_SESSION['peran']) ?>) | 
        <a href="logout.php">Logout</a>
    </p>

    <!-- Tombol Tambah Data HANYA muncul jika pengguna adalah admin -->
    <?php if ($_SESSION['peran'] === 'admin'): ?>
        <p><a href="tambah.php"><button>+ Tambah Data Ekspedisi</button></a></p>
    <?php endif; ?>

    <?php if ($pesanSukses): ?>
        <p style="color: green;"><b><?= e($pesanSukses) ?></b></p>
    <?php endif; ?>

    <table border="1" cellpadding="8" cellspacing="0">
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
            ?>
                <tr>
                    <!-- KOMENTAR PENJELAS LOGIKA SANITASI: 
                         Semua data yang dicetak (baik dari input pengguna maupun teks statis database)
                         WAJIB dibungkus fungsi escape e() untuk memblokir script berbahaya. -->
                    <td><?= $no++ ?></td>
                    <td><?= e($baris['lokasi_gunung']) ?></td>
                    <td><?= e($baris['status_jalur']) ?></td>
                    <td><?= e($baris['nama_penginput']) ?></td>
                    <td><a href="detail.php?id=<?= (int)$baris['id'] ?>">Lihat Detail</a></td>
                </tr>
            <?php endwhile; ?>
            
            <?php if ($hasil->num_rows === 0): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">Belum ada catatan ekspedisi.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>