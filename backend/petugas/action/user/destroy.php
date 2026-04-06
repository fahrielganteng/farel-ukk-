<?php
include '../../app.php';

if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Cek apakah user ada
    $checkUser = mysqli_query($connect, "SELECT * FROM users WHERE id = $id");
    if (mysqli_num_rows($checkUser) == 0) {
        echo "<script>alert('Data tidak ditemukan');window.location.href='../../pages/user/index.php';</script>";
        exit;
    }

    // Query hapus data
    $qDelete = "DELETE FROM users WHERE id = $id";
    $result = mysqli_query($connect, $qDelete);

    if ($result) {
        echo "<script>alert('Data Berhasil dihapus');window.location.href='../../pages/user/index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data: " . addslashes(mysqli_error($connect)) . "');window.location.href='../../pages/user/index.php';</script>";
    }
} else {
    echo "<script>alert('ID tidak ditemukan');window.location.href='../../pages/user/index.php';</script>";
}