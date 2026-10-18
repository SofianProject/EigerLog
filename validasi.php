<?php
/**
 * KOMENTAR PENJELAS LOGIKA VALIDASI PANJANG TEKS:
 * Memeriksa apakah panjang string setelah di-trim berada di antara $min dan $max.
 * Menggunakan mb_strlen() untuk menangani encoding UTF-8 secara akurat.
 */
function validasiPanjang($teks, $min, $max) {
    $panjang = mb_strlen(trim($teks), 'UTF-8');
    return ($panjang >= $min && $panjang <= $max);
}

/**
 * KOMENTAR PENJELAS LOGIKA VALIDASI WHITE LIST (ENUM):
 * Sesuai aturan spesifikasi (Bagian 3), pilihan ENUM wajib divalidasi di sisi server.
 * Fungsi in_array dengan parameter strict = true memastikan nilai input
 * benar-benar ada di dalam daftar putih ($daftarPutih) dan bukan input palsu dari Inspect Element.
 */
function validasiPilihan($nilai, $daftarPutih) {
    return in_array($nilai, $daftarPutih, true);
}

/**
 * KOMENTAR PENJELAS LOGIKA VALIDASI ANGKA:
 * Memeriksa apakah input merupakan bilangan bulat valid dan berada dalam rentang $min hingga $max.
 */
function validasiAngka($nilai, $min, $max) {
    if (!filter_var($nilai, FILTER_VALIDATE_INT) && $nilai !== 0 && $nilai !== '0') {
        return false;
    }
    $angka = (int)$nilai;
    return ($angka >= $min && $angka <= $max);
}

/**
 * KOMENTAR PENJELAS LOGIKA VALIDASI KHUSUS TEMA:
 * Memproses seluruh formulir input ekspedisi dan mengembalikan array berisi pesan error.
 * Menerapkan aturan umum dan aturan validasi silang/khusus tema.
 */
function validasiFormEkspedisi($data) {
    $errors = [];

    // Daftar putih status jalur (ENUM)
    $statusAllowed = ['Resmi', 'Tutup', 'Buka Bersyarat'];

    // 1. Validasi Lokasi Gunung (Wajib isi, 3 - 150 karakter)
    $lokasi = trim($data['lokasi_gunung'] ?? '');
    if (empty($lokasi)) {
        $errors['lokasi_gunung'] = "Lokasi gunung wajib diisi.";
    } elseif (!validasiPanjang($lokasi, 3, 150)) {
        $errors['lokasi_gunung'] = "Nama lokasi gunung harus terdiri dari 3 hingga 150 karakter.";
    }

    // 2. Validasi Status Jalur (White List ENUM)
    $status = $data['status_jalur'] ?? '';
    if (empty($status)) {
        $errors['status_jalur'] = "Status jalur wajib dipilih.";
    } elseif (!validasiPilihan($status, $statusAllowed)) {
        $errors['status_jalur'] = "Pilihan status jalur tidak valid.";
    }

    // 3. Validasi Ketinggian MDPL (Angka, 1 - 9000 MDPL)
    $ketinggian = $data['ketinggian_mdpl'] ?? '';
    if ($ketinggian === '') {
        $errors['ketinggian_mdpl'] = "Ketinggian MDPL wajib diisi.";
    } elseif (!validasiAngka($ketinggian, 1, 9000)) {
        $errors['ketinggian_mdpl'] = "Ketinggian MDPL harus berupa angka positif (1 - 9000).";
    }

    // 4. Validasi Deskripsi Catatan Khusus (Maksimal 2000 karakter)
    $catatan = trim($data['catatan_khusus'] ?? '');
    if (mb_strlen($catatan, 'UTF-8') > 2000) {
        $errors['catatan_khusus'] = "Catatan khusus maksimal 2000 karakter.";
    }

    // 5. ATURAN VALIDASI KHUSUS TEMA (Sesuai Spesifikasi Bagian 3):
    // Jika status_jalur = 'Buka Bersyarat', kolom catatan_khusus wajib diisi minimal 20 karakter.
    if ($status === 'Buka Bersyarat') {
        if (empty($catatan)) {
            $errors['catatan_khusus'] = "Jika status 'Buka Bersyarat', catatan khusus wajib diisi.";
        } elseif (!validasiPanjang($catatan, 20, 2000)) {
            $errors['catatan_khusus'] = "Untuk status 'Buka Bersyarat', catatan khusus harus diisi minimal 20 karakter (jelaskan persyaratan perizinan/lampiran).";
        }
    }

    return $errors;
}
?>