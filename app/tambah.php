<?php
require 'sesi.php';
require 'koneksi.php';
require 'validasi.php'; 

// HANYA Pengurus yang bisa akses
wajibPengurus();

$errors = [];
$sukses = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dataForm = [
        'kegiatan'         => $_POST['kegiatan'] ?? '',
        'lokasi_kegiatan'  => $_POST['lokasi_kegiatan'] ?? '',
        'tanggal_kegiatan' => $_POST['tanggal_kegiatan'] ?? '',
        'jenis_kegiatan'   => $_POST['jenis_kegiatan'] ?? '',
        'catatan_khusus'   => $_POST['catatan_khusus'] ?? ''
    ];

    $errors = validasiFormEkspedisi($dataForm);

    if (empty($errors)) {
        //ID pemilik data (pengguna_id) diambil MUTLAK dari $_SESSION['id'] yang didapat saat login.
        //Ini menjamin keamanan karena pengguna tidak bisa memalsukan siapa penginput data
        //lewat manipulasi elemen HTML form.
        $pengguna_id = $_SESSION['id'];
        $kegiatan = trim($dataForm['kegiatan']);
        $lokasi = trim($dataForm['lokasi_kegiatan']);
        $tanggal = trim($dataForm['tanggal_kegiatan']);
        $jenis = $dataForm['jenis_kegiatan'];
        $catatan = trim($dataForm['catatan_khusus']);

        try {
                        //Kueri ini tidak menyambung string PHP langsung ke SQL, melainkan memakai placeholder (?).
            //Nilai diikat (bind_param) dengan tipe 'i' (integer) dan 's' (string) untuk
            // menetralisir karakter berbahaya, sehingga mustahil terjadi SQL Injection.
            $stmt = $koneksi->prepare("INSERT INTO ekspedisi (pengguna_id, kegiatan, lokasi_kegiatan, tanggal_kegiatan, jenis_kegiatan, catatan_khusus) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $pengguna_id, $kegiatan, $lokasi, $tanggal, $jenis, $catatan);
            $stmt->execute();

            $_SESSION['flash_sukses'] = "Data kegiatan berhasil ditambahkan ke silabus!";
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kegiatan - EigerLog</title>
    <link rel="stylesheet" href="../css/tambah.css">
</head>
<body>

    <div class="form-container">
        <h2>Catat Kegiatan Baru</h2>
        <div class="subtitle">
            Pengurus yang bertugas: <b><?= e($_SESSION['nama']) ?></b> (<?= e($_SESSION['jabatan']) ?>)
        </div>

        <?php if (isset($errors['sistem'])): ?>
            <div class="system-error"><?= $errors['sistem'] ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nama Kegiatan (Misal: DIKSAR, Susur Gua)</label>
                <input type="text" name="kegiatan" 
                    class="form-control <?= isset($errors['kegiatan']) ? 'is-invalid' : '' ?>" 
                    value="<?= e($_POST['kegiatan'] ?? '') ?>" autocomplete="off">
                <?php if (isset($errors['kegiatan'])) echo "<span class='error-text'>{$errors['kegiatan']}</span>"; ?>
            </div>

            <div class="form-group">
                <label>Lokasi Kegiatan</label>
                <input type="text" name="lokasi_kegiatan" 
                    class="form-control <?= isset($errors['lokasi_kegiatan']) ? 'is-invalid' : '' ?>" 
                    value="<?= e($_POST['lokasi_kegiatan'] ?? '') ?>" autocomplete="off">
                <?php if (isset($errors['lokasi_kegiatan'])) echo "<span class='error-text'>{$errors['lokasi_kegiatan']}</span>"; ?>
            </div>

            <div class="form-group">
                <label>Tanggal Kegiatan</label>
                <input type="date" name="tanggal_kegiatan" 
                    class="form-control <?= isset($errors['tanggal_kegiatan']) ? 'is-invalid' : '' ?>" 
                    value="<?= e($_POST['tanggal_kegiatan'] ?? '') ?>">
                <?php if (isset($errors['tanggal_kegiatan'])) echo "<span class='error-text'>{$errors['tanggal_kegiatan']}</span>"; ?>
            </div>

            <div class="form-group">
                <label>Jenis Kegiatan</label>
                <?php $jenisPilihan = $_POST['jenis_kegiatan'] ?? ''; ?>
                <select name="jenis_kegiatan" class="form-control <?= isset($errors['jenis_kegiatan']) ? 'is-invalid' : '' ?>">
                    <option value="">-- Pilih Jenis --</option>
                    <option value="Resmi" <?= $jenisPilihan === 'Resmi' ? 'selected' : '' ?>>Resmi (Agenda Tahunan)</option>
                    <option value="Non Resmi" <?= $jenisPilihan === 'Non Resmi' ? 'selected' : '' ?>>Non Resmi (Insidental)</option>
                    <option value="Penjelajahan" <?= $jenisPilihan === 'Penjelajahan' ? 'selected' : '' ?>>Penjelajahan (Eksplorasi/Caving)</option>
                </select>
                <?php if (isset($errors['jenis_kegiatan'])) echo "<span class='error-text'>{$errors['jenis_kegiatan']}</span>"; ?>
            </div>

            <div class="form-group">
                <label>Catatan Khusus / Perlengkapan</label>
                <textarea name="catatan_khusus" rows="4" 
                    class="form-control <?= isset($errors['catatan_khusus']) ? 'is-invalid' : '' ?>"><?= e($_POST['catatan_khusus'] ?? '') ?></textarea>
                <?php if (isset($errors['catatan_khusus'])) echo "<span class='error-text'>{$errors['catatan_khusus']}</span>"; ?>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-submit">Simpan ke Silabus</button>
                <a href="daftar.php" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>

</body>
</html>