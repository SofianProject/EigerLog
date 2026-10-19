<?php
session_start();

//Fungsi e() digunakan untuk sanitasi keluaran (menghindari serangan XSS).
//Fungsi htmlspecialchars() mengubah karakter khusus HTML seperti < dan > 
// menjadi entitas teks biasa (&lt; dan &gt;).
// Wajib dipanggil setiap kali mencetak nilai dari database ke HTML.
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
//Fungsi penjaga untuk memastikan pengguna sudah login.
// Jika belum ada ID di sesi, paksa pindah ke halaman login.
function wajibLogin() {
    if (!isset($_SESSION['id'])) {
        header("Location: login.php");
        exit;
    }
}


//Memeriksa apakah pengguna memiliki hak (Admin, Komandan, Dewan Kehormatan).
// Jika jabatannya hanya 'Anggota' atau tidak ada, tendang ke halaman daftar.

function wajibPengurus() {
    wajibLogin();
    
    // Daftar jabatan yang diizinkan mengelola data logbook
    $jabatanDiizinkan = [
    'admin', 
    'Komandan', 
    'Wakil Komandan', 
    'Dewan Kehormatan', 
    'Panglima Divisi Gunung Hutan', 
    'Panglima Divisi Panjat Susur'
];
    
    // Ganti 'peran' menjadi 'jabatan' sesuai kolom tabel yang baru
    if (!in_array($_SESSION['jabatan'] ?? '', $jabatanDiizinkan, true)) {
        header("Location: daftar.php");
        exit;
    }
}
?>