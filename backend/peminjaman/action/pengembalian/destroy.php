<?php
session_start();
include '../../app.php';

if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Ambil data pengembalian untuk mendapatkan peminjaman_id
    $checkPengembalian = mysqli_query($connect, "SELECT peminjaman_id FROM pengembalian WHERE id = $id");
    if (mysqli_num_rows($checkPengembalian) == 0) {
        $_SESSION['error'] = "Data tidak ditemukan";
        header("Location: ../../pages/pengembalian/index.php");
        exit;
    }
    
    $data = mysqli_fetch_assoc($checkPengembalian);
    $peminjaman_id = $data['peminjaman_id'];

    // Query hapus data pengembalian
    $qDelete = "DELETE FROM pengembalian WHERE id = $id";
    $result = mysqli_query($connect, $qDelete);

    if ($result) {
        // Update status peminjaman kembali menjadi 'dipinjam'
        $qUpdatePeminjaman = "UPDATE peminjaman SET status = 'dipinjam' WHERE id = '$peminjaman_id'";
        mysqli_query($connect, $qUpdatePeminjaman);
        
        $_SESSION['success'] = "Data pengembalian berhasil dihapus!";
        header("Location: ../../pages/pengembalian/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal menghapus data: " . mysqli_error($connect);
        header("Location: ../../pages/pengembalian/index.php");
        exit;
    }
} else {
    $_SESSION['error'] = "ID tidak ditemukan";
    header("Location: ../../pages/pengembalian/index.php");
    exit;
}
?>