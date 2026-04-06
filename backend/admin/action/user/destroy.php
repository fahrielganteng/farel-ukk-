<?php
session_start();
include '../../app.php';

if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Cek apakah user ada
    $checkUser = mysqli_query($connect, "SELECT * FROM users WHERE id = $id");
    if (mysqli_num_rows($checkUser) == 0) {
        $_SESSION['error'] = "User dengan ID $id tidak ditemukan";
        header("Location: ../../pages/user/index.php");
        exit;
    }

    // Ambil data user untuk pesan konfirmasi
    $userData = mysqli_fetch_assoc($checkUser);
    $username = $userData['username'];

    // Query hapus data
    $qDelete = "DELETE FROM users WHERE id = $id";
    $result = mysqli_query($connect, $qDelete);

    if ($result) {
        $_SESSION['success'] = "User ID $id '$username' berhasil dihapus";
        header("Location: ../../pages/user/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal menghapus data: " . mysqli_error($connect);
        header("Location: ../../pages/user/index.php");
        exit;
    }
} else {
    $_SESSION['error'] = "ID tidak ditemukan";
    header("Location: ../../pages/user/index.php");
    exit;
}
?>