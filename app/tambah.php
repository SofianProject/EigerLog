<?php
require 'sesi.php';
require 'koneksi.php';
require 'validasi.php'; 

wajibAdmin();

$errors = [];
$sukses = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dataForm = [
        'lokasi_gunung' => $_POST['lokasi_gunung'] ?? '',
        'status_jalur'  => $_POST['status_jalur'] ?? '',
        'ketinggian_mdpl'=> $_POST['ketinggian_mdpl'] ?? '',
        'catatan_khusus' => $_POST['catatan_khusus'] ?? ''
    ];

    $errors = validasiFormEkspedisi($dataForm);

    if (empty($errors)) {
        $pengguna_id = $_SESSION['id'];
        $lokasi = trim($dataForm['lokasi_gunung']);
        $status = $dataForm['status_jalur'];
        $mdpl = (int)$dataForm['ketinggian_mdpl'];
        $catatan = trim($dataForm['catatan_khusus']);

        try {
            $stmt = $koneksi->prepare("INSERT INTO ekspedisi (pengguna_id, lokasi_gunung, status_jalur, ketinggian_mdpl, catatan_khusus) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issis", $pengguna_id, $lokasi, $status, $mdpl, $catatan);
            $stmt->execute();

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Logbook - EigerLog</title>
    <link rel="stylesheet" href="../css/tambah.css">
</head>
<body>

    <div class="form-container">
        <h2>Catat Rute Ekspedisi Baru</h2>
        <div class="subtitle">
            Admin yang bertugas: <b><?= e($_SESSION['nama']) ?></b>
        </div>

        <?php if (isset($errors['sistem'])): ?>
            <div class="system-error"><?= $errors['sistem'] ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Lokasi / Nama Gunung</label>
                <!-- Menambahkan class is-invalid jika ada error di lokasi_gunung -->
                <input type="text" name="lokasi_gunung" 
                       class="form-control <?= isset($errors['lokasi_gunung']) ? 'is-invalid' : '' ?>" 
                       value="<?= e($_POST['lokasi_gunung'] ?? '') ?>" autocomplete="off">
                <?php if (isset($errors['lokasi_gunung'])): ?>
                    <span class="error-text"><?= $errors['lokasi_gunung'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Status Jalur</label>
                <?php $statusPilihan = $_POST['status_jalur'] ?? ''; ?>
                <select name="status_jalur" class="form-control <?= isset($errors['status_jalur']) ? 'is-invalid' : '' ?>">
                    <option value="">-- Pilih Status Rute --</option>
                    <option value="Resmi" <?= $statusPilihan === 'Resmi' ? 'selected' : '' ?>>Resmi (Terbuka untuk umum)</option>
                    <option value="Tutup" <?= $statusPilihan === 'Tutup' ? 'selected' : '' ?>>Tutup (Dilarang melintas)</option>
                    <option value="Buka Bersyarat" <?= $statusPilihan === 'Buka Bersyarat' ? 'selected' : '' ?>>Buka Bersyarat (Butuh izin khusus)</option>
                </select>
                <?php if (isset($errors['status_jalur'])): ?>
                    <span class="error-text"><?= $errors['status_jalur'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Ketinggian (MDPL)</label>
                <input type="number" name="ketinggian_mdpl" 
                       class="form-control <?= isset($errors['ketinggian_mdpl']) ? 'is-invalid' : '' ?>" 
                       value="<?= e($_POST['ketinggian_mdpl'] ?? '') ?>">
                <?php if (isset($errors['ketinggian_mdpl'])): ?>
                    <span class="error-text"><?= $errors['ketinggian_mdpl'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Catatan Khusus / Syarat Perizinan</label>
                <textarea name="catatan_khusus" rows="5" 
                          class="form-control <?= isset($errors['catatan_khusus']) ? 'is-invalid' : '' ?>"><?= e($_POST['catatan_khusus'] ?? '') ?></textarea>
                <?php if (isset($errors['catatan_khusus'])): ?>
                    <span class="error-text"><?= $errors['catatan_khusus'] ?></span>
                <?php endif; ?>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-submit">Simpan ke Logbook</button>
                <a href="daftar.php" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>

</body>
</html>