<?php
require 'sesi.php';

// Hapus semua data pada $_SESSION
$_SESSION = [];

// Jika menggunakan cookie sesi, hapus juga cookie-nya
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan sesi di server
session_destroy();

// Arahkan kembali ke halaman login
header("Location: login.php");
exit;
?>