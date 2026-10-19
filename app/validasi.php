<?php
// Helper untuk memvalidasi panjang string
function validasiPanjang($teks, $min, $max) {
    $panjang = mb_strlen(trim($teks), 'UTF-8');
    return ($panjang >= $min && $panjang <= $max);
}

// Helper untuk memvalidasi daftar putih (ENUM)
function validasiPilihan($nilai, $daftarPutih) {
    return in_array($nilai, $daftarPutih, true);
}

// Helper BARU: Validasi format tanggal YYYY-MM-DD
function validasiTanggal($tanggal) {
    $d = DateTime::createFromFormat('Y-m-d', $tanggal);
    return $d && $d->format('Y-m-d') === $tanggal;
}

//Memeriksa form input yang baru: kegiatan, lokasi, tanggal, jenis_kegiatan.
function validasiFormEkspedisi($data) {
    $errors = [];
    $jenisAllowed = ['Resmi', 'Non Resmi', 'Penjelajahan'];

    // 1. Validasi Nama Kegiatan
    $kegiatan = trim($data['kegiatan'] ?? '');
    if (empty($kegiatan)) {
        $errors['kegiatan'] = "Nama kegiatan wajib diisi.";
    } elseif (!validasiPanjang($kegiatan, 3, 150)) {
        $errors['kegiatan'] = "Nama kegiatan harus 3 - 150 karakter.";
    }

    // 2. Validasi Lokasi
    $lokasi = trim($data['lokasi_kegiatan'] ?? '');
    if (empty($lokasi)) {
        $errors['lokasi_kegiatan'] = "Lokasi kegiatan wajib diisi.";
    } elseif (!validasiPanjang($lokasi, 3, 150)) {
        $errors['lokasi_kegiatan'] = "Lokasi harus 3 - 150 karakter.";
    }
    
    // 3. Validasi Tanggal Kegiatan
    $tanggal = $data['tanggal_kegiatan'] ?? '';
    if (empty($tanggal)) {
        $errors['tanggal_kegiatan'] = "Tanggal kegiatan wajib dipilih.";
    } elseif (!validasiTanggal($tanggal)) {
        $errors['tanggal_kegiatan'] = "Format tanggal tidak valid.";
    }

    // 4. Validasi Jenis Kegiatan (White List ENUM)
    $jenis = $data['jenis_kegiatan'] ?? '';
    if (empty($jenis)) {
        $errors['jenis_kegiatan'] = "Jenis kegiatan wajib dipilih.";
    } elseif (!validasiPilihan($jenis, $jenisAllowed)) {
        $errors['jenis_kegiatan'] = "Pilihan jenis kegiatan tidak valid.";
    }

    // 5. Validasi Catatan Khusus
    $catatan = trim($data['catatan_khusus'] ?? '');
    if (mb_strlen($catatan, 'UTF-8') > 2000) {
        $errors['catatan_khusus'] = "Catatan khusus maksimal 2000 karakter.";
    }

    // Jika jenis_kegiatan = 'Penjelajahan', catatan_khusus wajib diisi minimal 20 karakter 
    if ($jenis === 'Penjelajahan') {
        if (empty($catatan)) {
            $errors['catatan_khusus'] = "Untuk Penjelajahan (Caving/dll), catatan wajib diisi.";
        } elseif (!validasiPanjang($catatan, 20, 2000)) {
            $errors['catatan_khusus'] = "Catatan Penjelajahan minimal 20 karakter (rinci perlengkapan teknis yang digunakan).";
        }
    }

    return $errors;
}
?>