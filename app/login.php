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

        // Verifikasi sandi dengan fungsi bawaan PHP yang aman
        if ($pengguna && password_verify($sandi, $pengguna['hash_sandi'])) {
            $_SESSION['id'] = $pengguna['id'];
            $_SESSION['nama'] = $pengguna['nama'];
            $_SESSION['peran'] = $pengguna['peran'];
            
            session_regenerate_id(true);
            
            header("Location: daftar.php");
            exit;
        } else {
            $error = "Username atau sandi salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EigerLog</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Login EigerLog</h2>
        
        <?php if ($error): ?>
            <div class="error-msg"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>" required autocomplete="off">
            </div>
            
            <div class="form-group">
                <label>Kata Sandi</label>
                <input type="password" name="sandi" required>
            </div>
            
            <button type="submit">Masuk</button>
        </form>
        
        <div class="link-register">
            Belum punya akun? <a href="registrasi.php">Daftar di sini</a>.
        </div>
    </div>
</body>
</html>