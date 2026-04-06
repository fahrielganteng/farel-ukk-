<?php
// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

// Include koneksi database
include '../../app.php';

// Fungsi untuk escape string
function escapeString($string) {
    global $connect;
    return mysqli_real_escape_string($connect, $string);
}

if (isset($_POST['tombol'])) {
    // Ambil data dari form dengan validasi
    $kode_peminjaman = isset($_POST['kode_peminjaman']) ? escapeString($_POST['kode_peminjaman']) : '';
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $barang_id = isset($_POST['barang_id']) ? (int)$_POST['barang_id'] : 0;
    $jumlah = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 0;
    $tgl_pinjam = isset($_POST['tgl_pinjam']) ? escapeString($_POST['tgl_pinjam']) : '';
    $tgl_kembali_rencana = isset($_POST['tgl_kembali_rencana']) ? escapeString($_POST['tgl_kembali_rencana']) : '';
    $status = isset($_POST['status']) ? escapeString($_POST['status']) : 'pending';
    
    // Validasi input wajib
    if (empty($kode_peminjaman) || $user_id <= 0 || $barang_id <= 0 || $jumlah <= 0 || 
        empty($tgl_pinjam) || empty($tgl_kembali_rencana)) {
        $_SESSION['error'] = "Semua field wajib diisi dengan benar!";
        header("Location: ../../pages/peminjaman/create.php");
        exit;
    }
    
    // Hitung lama pinjam (dalam hari)
    $tgl1 = new DateTime($tgl_pinjam);
    $tgl2 = new DateTime($tgl_kembali_rencana);
    $interval = $tgl1->diff($tgl2);
    $lama_pinjam = $interval->days;
    
    // Jika tanggal kembali lebih kecil dari tanggal pinjam
    if ($lama_pinjam < 0) {
        $_SESSION['error'] = "Tanggal kembali tidak boleh lebih kecil dari tanggal pinjam!";
        header("Location: ../../pages/peminjaman/create.php");
        exit;
    }
    
    // Query insert
    $qInsert = "INSERT INTO peminjaman 
                (kode_peminjaman, user_id, barang_id, jumlah, tgl_pinjam, tgl_kembali_rencana, lama_pinjam, status) 
                VALUES 
                ('$kode_peminjaman', $user_id, $barang_id, $jumlah, '$tgl_pinjam', '$tgl_kembali_rencana', $lama_pinjam, '$status')";

    if (mysqli_query($connect, $qInsert)) {
        // Update stok barang jika status langsung 'approved'
        if ($status == 'approved') {
            $updateStok = "UPDATE barang SET jumlah = jumlah - $jumlah WHERE id = $barang_id";
            mysqli_query($connect, $updateStok);
        }
        
        $_SESSION['success'] = "Peminjaman berhasil ditambahkan!";
        header("Location: ../../pages/peminjaman/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal menambahkan peminjaman: " . mysqli_error($connect);
        header("Location: ../../pages/peminjaman/create.php");
        exit;
    }
} else {
    // Jika akses langsung ke file tanpa submit form
    $_SESSION['error'] = "Akses tidak valid!";
    header("Location: ../../pages/peminjaman/create.php");
    exit;
}
?>