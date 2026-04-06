<?php
// destroy.php
session_start();
include '../../app.php';

if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Cek apakah peminjaman ada
    $checkPeminjaman = mysqli_query($connect, "SELECT * FROM peminjaman WHERE id = $id");
    if (mysqli_num_rows($checkPeminjaman) == 0) {
        $_SESSION['error'] = "Data peminjaman tidak ditemukan";
        header("Location: ../../pages/peminjaman/index.php");
        exit;
    }

    // Ambil data peminjaman
    $peminjaman = mysqli_fetch_assoc($checkPeminjaman);
    
    // Kembalikan stok barang jika peminjaman belum dikembalikan
    if ($peminjaman['status'] != 'dikembalikan') {
        $barang_id = $peminjaman['barang_id'];
        $jumlah = $peminjaman['jumlah'];
        
        // Update stok barang
        mysqli_query($connect, "UPDATE barang SET stok = stok + $jumlah WHERE id = $barang_id");
    }

    // Query hapus data peminjaman
    $qDelete = "DELETE FROM peminjaman WHERE id = $id";
    $result = mysqli_query($connect, $qDelete);

    if ($result) {
        $_SESSION['success'] = "Data peminjaman berhasil dihapus";
        header("Location: ../../pages/peminjaman/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal menghapus data: " . mysqli_error($connect);
        header("Location: ../../pages/peminjaman/index.php");
        exit;
    }
} else {
    $_SESSION['error'] = "ID tidak ditemukan";
    header("Location: ../../pages/peminjaman/index.php");
    exit;
}
?>