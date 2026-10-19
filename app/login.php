<?php
require 'sesi.php';
require 'koneksi.php';

// untuk mengecek apakah sudah login atau belum, jika sudah akan langsung dialihkan ke halaman daftar.php
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
        $stmt = $koneksi->prepare("SELECT id, nama, jabatan, hash_sandi FROM pengguna WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $hasil = $stmt->get_result();
        
        $pengguna = $hasil->fetch_assoc();

        // verifikai sandi menggunakan password_verify untuk mengecek apakah sandi yang di masukkan sesuai dengan has yang ada di database
        // di hash yang ada di database di buat menggunakan has generator
        // akan muncul pesan error ketika user ataupun password yang di masukkan tidak sesuai dengan database
        // pesan error yang muncul sama baik ketika username salah atau passwor dyang salah untuk menghindari kebobolan
        if ($pengguna && password_verify($sandi, $pengguna['hash_sandi'])) {
            $_SESSION['id'] = $pengguna['id'];
            $_SESSION['nama'] = $pengguna['nama'];
            $_SESSION['jabatan'] = $pengguna['jabatan'];
            
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