<?php
include '../../app.php';

if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Cek apakah peminjaman ada
    $checkPeminjaman = mysqli_query($connect, "SELECT * FROM peminjaman WHERE id = $id");
    if (mysqli_num_rows($checkPeminjaman) == 0) {
        echo "<script>alert('Data peminjaman tidak ditemukan');window.location.href='../../pages/peminjaman/index.php';</script>";
        exit;
    }

    // Ambil data peminjaman untuk info
    $peminjaman = mysqli_fetch_assoc($checkPeminjaman);
    $kode_peminjaman = $peminjaman['kode_peminjaman'];
    
    // Cek apakah ada data pengembalian terkait
    $checkPengembalian = mysqli_query($connect, "SELECT * FROM pengembalian WHERE peminjaman_id = $id");
    $hasPengembalian = mysqli_num_rows($checkPengembalian) > 0;
    
    // Jika ada pengembalian, hapus dulu data pengembalian
    if ($hasPengembalian) {
        // Hapus data pengembalian terlebih dahulu
        $deletePengembalian = mysqli_query($connect, "DELETE FROM pengembalian WHERE peminjaman_id = $id");
        
        if (!$deletePengembalian) {
            echo "<script>alert('Gagal menghapus data pengembalian terkait: " . addslashes(mysqli_error($connect)) . "');window.location.href='../../pages/peminjaman/index.php';</script>";
            exit;
        }
    }
    
    // Kembalikan stok barang jika peminjaman sedang aktif
    if ($peminjaman['status'] == 'dipinjam' || $peminjaman['status'] == 'disetujui') {
        $barang_id = $peminjaman['barang_id'];
        $jumlah = $peminjaman['jumlah'];
        
        // Update stok barang
        mysqli_query($connect, "UPDATE barang SET stok = stok + $jumlah WHERE id = $barang_id");
    }

    // Query hapus data peminjaman
    $qDelete = "DELETE FROM peminjaman WHERE id = $id";
    $result = mysqli_query($connect, $qDelete);

    if ($result) {
        echo "<script>alert('Data peminjaman berhasil dihapus');window.location.href='../../pages/peminjaman/index.php';</script>";
    } else {
        // Jika masih gagal, coba hapus dengan CASCADE atau nonaktifkan foreign key sementara
        mysqli_query($connect, "SET FOREIGN_KEY_CHECKS = 0");
        $result = mysqli_query($connect, $qDelete);
        mysqli_query($connect, "SET FOREIGN_KEY_CHECKS = 1");
        
        if ($result) {
            echo "<script>alert('Data peminjaman berhasil dihapus');window.location.href='../../pages/peminjaman/index.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus data: " . addslashes(mysqli_error($connect)) . "');window.location.href='../../pages/peminjaman/index.php';</script>";
        }
    }
} else {
    echo "<script>alert('ID tidak ditemukan');window.location.href='../../pages/peminjaman/index.php';</script>";
}
?>