<?php
include '../../app.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (isset($_POST['tombol'])) {
    $nama_kategori = escapeString($_POST['nama_kategori']);
    $deskripsi = escapeString($_POST['deskripsi']);

    // Query update
    $qUpdate = "UPDATE kategori SET 
                nama_kategori = '$nama_kategori', 
                deskripsi = '$deskripsi' 
                WHERE id = $id";

    $result = mysqli_query($connect, $qUpdate);
    if ($result) {
        $_SESSION['success'] = "Data kategori berhasil diupdate!";
        header("Location: ../../pages/kategori/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal mengupdate kategori: " . mysqli_error($connect);
        header("Location: ../../pages/kategori/edit.php?id=$id");
        exit;
    }
}
?>