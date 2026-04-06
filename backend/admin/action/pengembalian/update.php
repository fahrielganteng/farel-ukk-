<?php
session_start();
include '../../app.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (isset($_POST['tombol'])) {
    $peminjaman_id = escapeString($_POST['peminjaman_id']);
    $tgl_kembali = escapeString($_POST['tgl_kembali']);
    $kondisi = escapeString($_POST['kondisi']);
    $denda = escapeString($_POST['denda']);
    $keterangan = escapeString($_POST['keterangan']);

    $qUpdate = "UPDATE pengembalian SET 
                peminjaman_id = '$peminjaman_id', 
                tgl_kembali = '$tgl_kembali', 
                kondisi = '$kondisi', 
                denda = '$denda', 
                keterangan = '$keterangan' 
                WHERE id = $id";

    $result = mysqli_query($connect, $qUpdate);
    if ($result) {
        $_SESSION['success'] = "Data pengembalian berhasil diupdate!";
        header("Location: ../../pages/pengembalian/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal mengupdate pengembalian: " . mysqli_error($connect);
        header("Location: ../../pages/pengembalian/edit.php?id=$id");
        exit;
    }
}
?>