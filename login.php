<?php
require 'sesi.php';
require 'koneksi.php';

// Jika sudah login, tendang langsung ke halaman daftar
if (isset($_SESSION['id'])) {
    header("Location: daftar.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $sandi = $_POST['sandi'] ?? '';

    if (empty($username) || empty($sandi)) {
        $error = "Username dan sandi wajib diisi.";
    } else {
        $stmt = $koneksi->prepare("SELECT id, nama, peran, hash_sandi FROM pengguna WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $hasil = $stmt->get_result();
        
        $pengguna = $hasil->fetch_assoc();

        /**
         * KOMENTAR PENJELAS LOGIKA AUTENTIKASI (VERIFIKASI):
         * password_verify() mencocokkan input sandi dengan hash yang ada di database.
         * Pesan error sengaja DIBUAT SAMA baik ketika username tidak ditemukan
         * maupun saat sandi salah. Ini mencegah attacker melakukan 'Username Enumeration'
         * (menebak-nebak username mana yang terdaftar).
         */
        if ($pengguna && password_verify($sandi, $pengguna['hash_sandi'])) {
            // Login sukses, atur variabel sesi
            $_SESSION['id'] = $pengguna['id'];
            $_SESSION['nama'] = $pengguna['nama'];
            $_SESSION['peran'] = $pengguna['peran'];
            
            /**
             * KOMENTAR PENJELAS LOGIKA KEAMANAN SESI:
             * Mencegah serangan Session Fixation dengan mengganti ID sesi lama
             * dengan ID sesi baru setelah pengguna berhasil login.
             */
            session_regenerate_id(true);
            
            header("Location: daftar.php");
            exit;
        } else {
            // Pesan error seragam
            $error = "Username atau sandi salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login Pengguna</title>
</head>
<body>
    <h2>Login EigerLog</h2>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
    
    <form method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>" required><br><br>
        
        <label>Kata Sandi:</label><br>
        <input type="password" name="sandi" required><br><br>
        
        <button type="submit">Login</button>
    </form>
    <p>Belum punya akun? <a href="registrasi.php">Daftar di sini</a>.</p>
</body>
</html>