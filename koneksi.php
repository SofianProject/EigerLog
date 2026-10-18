<?php
// Pengaturan kredensial basis data XAMPP standar
$host = 'localhost';
$user = 'root';
$pass = ''; // Kosongkan jika menggunakan default XAMPP
$db   = 'eigerlog_db';

// Mengubah mode pelaporan MySQLi agar melemparkan Exception saat terjadi error
// Ini wajib diaktifkan agar blok try-catch bisa menangkap error koneksi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Membuka koneksi baru ke MySQLi
    $koneksi = new mysqli($host, $user, $pass, $db);
    
    // Mengatur set karakter ke utf8mb4 agar mendukung semua karakter teks dengan aman
    $koneksi->set_charset('utf8mb4');
} catch (Exception $e) {
    // KOMENTAR PENJELAS LOGIKA:
    // Menuliskan detail error ke log server (error_log) untuk keperluan *debugging* oleh developer.
    // Pengguna akhir hanya diberikan pesan umum demi keamanan agar struktur database tidak terekspos.
    error_log($e->getMessage());
    die("Maaf, terjadi kendala saat menghubungi basis data. Silakan coba beberapa saat lagi.");
}
?>