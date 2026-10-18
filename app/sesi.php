<?php
// Memulai sesi PHP agar variabel $_SESSION bisa digunakan
session_start();

/**
 * KOMENTAR PENJELAS LOGIKA SANITASI:
 * Fungsi e() digunakan untuk sanitasi keluaran (menghindari serangan XSS).
 * Fungsi htmlspecialchars() mengubah karakter khusus HTML seperti < dan > 
 * menjadi entitas teks biasa (&lt; dan &gt;).
 * Wajib dipanggil setiap kali mencetak nilai dari database ke HTML.
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * KOMENTAR PENJELAS LOGIKA AUTENTIKASI:
 * Fungsi penjaga untuk memastikan pengguna sudah login.
 * Jika belum ada ID di sesi, paksa pindah ke halaman login.
 */
function wajibLogin() {
    if (!isset($_SESSION['id'])) {
        header("Location: login.php");
        exit;
    }
}

/**
 * KOMENTAR PENJELAS LOGIKA OTORISASI PERAN:
 * Fungsi penjaga khusus untuk halaman 'Tambah Data' (tambah.php).
 * Memastikan yang mengakses memiliki peran 'admin'.
 * Jika anggota biasa mencoba mengakses, tendang kembali ke halaman daftar.
 */
function wajibAdmin() {
    // Pastikan sudah login terlebih dahulu
    wajibLogin();
    
    // Cek peran dari variabel sesi
    if ($_SESSION['peran'] !== 'admin') {
        header("Location: daftar.php");
        exit;
    }
}
?>