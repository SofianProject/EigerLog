<?php
require 'sesi.php';
require 'koneksi.php';
require 'validasi.php'; // Memanggil fungsi helper dari Langkah 3

// KOMENTAR PENJELAS LOGIKA:
// Memastikan hanya pengguna dengan peran 'admin' yang bisa membuka halaman ini.
// Fungsi ini (didefinisikan di sesi.php) akan langsung me-redirect anggota biasa.
wajibAdmin();

$errors = [];
$sukses = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Kumpulkan data dari formulir
    $dataForm = [
        'lokasi_gunung' => $_POST['lokasi_gunung'] ?? '',
        'status_jalur'  => $_POST['status_jalur'] ?? '',
        'ketinggian_mdpl'=> $_POST['ketinggian_mdpl'] ?? '',
        'catatan_khusus' => $_POST['catatan_khusus'] ?? ''
    ];

    // 2. Lakukan validasi server-side
    $errors = validasiFormEkspedisi($dataForm);

    // 3. Jika lolos validasi (tidak ada error), masukkan ke basis data
    if (empty($errors)) {
        /**
         * KOMENTAR PENJELAS LOGIKA PENYIMPANAN DATA:
         * ID pemilik data (pengguna_id) diambil MUTLAK dari $_SESSION['id'] yang didapat saat login.
         * Ini menjamin keamanan karena pengguna tidak bisa memalsukan siapa penginput data
         * lewat manipulasi elemen HTML form.
         */
        $pengguna_id = $_SESSION['id'];
        
        $lokasi = trim($dataForm['lokasi_gunung']);
        $status = $dataForm['status_jalur'];
        $mdpl = (int)$dataForm['ketinggian_mdpl'];
        $catatan = trim($dataForm['catatan_khusus']);

        try {
            /**
             * KOMENTAR PENJELAS LOGIKA PREPARED STATEMENT:
             * Kueri ini tidak menyambung string PHP langsung ke SQL, melainkan memakai placeholder (?).
             * Nilai diikat (bind_param) dengan tipe 'i' (integer) dan 's' (string) untuk
             * menetralisir karakter berbahaya, sehingga mustahil terjadi SQL Injection.
             */
            $stmt = $koneksi->prepare("INSERT INTO ekspedisi (pengguna_id, lokasi_gunung, status_jalur, ketinggian_mdpl, catatan_khusus) VALUES (?, ?, ?, ?, ?)");
            // i = integer (pengguna_id), s = string (lokasi), s = string (status), i = integer (mdpl), s = string (catatan)
            $stmt->bind_param("issis", $pengguna_id, $lokasi, $status, $mdpl, $catatan);
            $stmt->execute();

            // Menerapkan Pola PRG (Post-Redirect-Get) untuk mencegah resubmission form saat refresh
            // Flash message diset untuk memberi notifikasi ke halaman daftar
            $_SESSION['flash_sukses'] = "Data ekspedisi baru berhasil ditambahkan!";
            header("Location: daftar.php");
            exit;
            
        } catch (Exception $e) {
            error_log($e->getMessage());
            $errors['sistem'] = "Terjadi kegagalan saat menyimpan data. Silakan coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Logbook Ekspedisi</title>
</head>
<body>
    <h2>Tambah Data Logbook Ekspedisi</h2>
    <p>Halo Admin <b><?= e($_SESSION['nama']) ?></b>, silakan catat rute baru.</p>

    <!-- Menampilkan pesan error sistem jika database bermasalah -->
    <?php if (isset($errors['sistem'])) echo "<p style='color:red;'>{$errors['sistem']}</p>"; ?>

    <!-- 
      Sticky Form: Menampilkan kembali nilai (value) yang sudah diisi pengguna sebelumnya 
      sehingga jika ada error validasi, ketikan mereka tidak hilang. 
      Semua nilai yang dicetak wajib di-escape dengan fungsi e().
    -->
    <form method="POST">
        <div>
            <label>Lokasi / Nama Gunung:</label><br>
            <input type="text" name="lokasi_gunung" value="<?= e($_POST['lokasi_gunung'] ?? '') ?>">
            <?php if (isset($errors['lokasi_gunung'])) echo "<span style='color:red;'> {$errors['lokasi_gunung']}</span>"; ?>
        </div>
        <br>

        <div>
            <label>Status Jalur:</label><br>
            <?php $statusPilihan = $_POST['status_jalur'] ?? ''; ?>
            <select name="status_jalur">
                <option value="">-- Pilih Status --</option>
                <option value="Resmi" <?= $statusPilihan === 'Resmi' ? 'selected' : '' ?>>Resmi</option>
                <option value="Tutup" <?= $statusPilihan === 'Tutup' ? 'selected' : '' ?>>Tutup</option>
                <option value="Buka Bersyarat" <?= $statusPilihan === 'Buka Bersyarat' ? 'selected' : '' ?>>Buka Bersyarat</option>
            </select>
            <?php if (isset($errors['status_jalur'])) echo "<span style='color:red;'> {$errors['status_jalur']}</span>"; ?>
        </div>
        <br>

        <div>
            <label>Ketinggian (MDPL):</label><br>
            <input type="number" name="ketinggian_mdpl" value="<?= e($_POST['ketinggian_mdpl'] ?? '') ?>">
            <?php if (isset($errors['ketinggian_mdpl'])) echo "<span style='color:red;'> {$errors['ketinggian_mdpl']}</span>"; ?>
        </div>
        <br>

        <div>
            <label>Catatan Khusus:</label><br>
            <textarea name="catatan_khusus" rows="4" cols="50"><?= e($_POST['catatan_khusus'] ?? '') ?></textarea>
            <?php if (isset($errors['catatan_khusus'])) echo "<span style='color:red;'> {$errors['catatan_khusus']}</span>"; ?>
        </div>
        <br>

        <button type="submit">Simpan Data</button>
        <a href="daftar.php">Batal & Kembali</a>
    </form>
</body>
</html>