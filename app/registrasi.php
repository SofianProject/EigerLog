<?php
require 'sesi.php';
require 'koneksi.php';

$error = '';
$sukses = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $sandi = $_POST['sandi'] ?? '';

    if (empty($username) || empty($nama) || empty($sandi)) {
        $error = "Semua kolom wajib diisi.";
    } else {
        $hash_sandi = password_hash($sandi, PASSWORD_DEFAULT);

        try {
            $stmt = $koneksi->prepare("INSERT INTO pengguna (username, nama, hash_sandi) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $nama, $hash_sandi);
            $stmt->execute();
            
            $sukses = "Registrasi berhasil! Silakan login.";
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $error = "Maaf, username '$username' sudah dipakai. Silakan pilih username lain.";
            } else {
                error_log($e->getMessage());
                $error = "Terjadi kesalahan sistem saat mendaftar.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun - EigerLog</title>
    <link rel="stylesheet" href="../css/registrasi.css">
</head>
<body>
    <div class="register-container">
        <h2>Registrasi Anggota</h2>
        
        <?php if ($error): ?>
            <div class="error-msg"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($sukses): ?>
            <div class="success-msg"><?= $sukses ?> <br><a href="login.php">Ke halaman login</a></div>
        <?php endif; ?>
        
        <!-- Form disembunyikan jika registrasi sukses agar tampilan bersih -->
        <?php if (!$sukses): ?>
            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>" required autocomplete="off">
                </div>
                
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" value="<?= e($_POST['nama'] ?? '') ?>" required autocomplete="off">
                </div>
                
                <div class="form-group">
                    <label>Kata Sandi</label>
                    <input type="password" name="sandi" required>
                </div>
                
                <button type="submit">Daftar Akun</button>
            </form>
        <?php endif; ?>
        
        <div class="link-login">
            Sudah punya akun? <a href="login.php">Login di sini</a>.
        </div>
    </div>
</body>
</html>