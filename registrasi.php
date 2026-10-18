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
        /**
         * KOMENTAR PENJELAS LOGIKA AUTENTIKASI (HASH):
         * Sandi tidak boleh disimpan dalam bentuk plain-text.
         * Menggunakan password_hash() dengan algoritma bcrypt secara otomatis
         * membuat salt yang unik sehingga aman dari serangan rainbow table.
         */
        $hash_sandi = password_hash($sandi, PASSWORD_DEFAULT);

        try {
            $stmt = $koneksi->prepare("INSERT INTO pengguna (username, nama, hash_sandi) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $nama, $hash_sandi);
            $stmt->execute();
            
            $sukses = "Registrasi berhasil! Silakan login.";
        } catch (mysqli_sql_exception $e) {
            /**
             * KOMENTAR PENJELAS LOGIKA PENANGANAN ERROR UNIK:
             * Menangkap pelanggaran constraint UNIQUE (kode error 1062).
             * Jika username sudah ada di database, jangan tampilkan error SQL kotor,
             * tetapi berikan pesan yang ramah kepada pengguna.
             */
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
    <title>Registrasi Akun</title>
</head>
<body>
    <h2>Registrasi Anggota Baru</h2>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if ($sukses) echo "<p style='color:green;'>$sukses <a href='login.php'>Ke halaman login</a></p>"; ?>
    
    <form method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>" required><br><br>
        
        <label>Nama Lengkap:</label><br>
        <input type="text" name="nama" value="<?= e($_POST['nama'] ?? '') ?>" required><br><br>
        
        <label>Kata Sandi:</label><br>
        <input type="password" name="sandi" required><br><br>
        
        <button type="submit">Daftar</button>
    </form>
    <p>Sudah punya akun? <a href="login.php">Login di sini</a>.</p>
</body>
</html>