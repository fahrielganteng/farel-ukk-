<?php
include '../../app.php';

if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Cek apakah kategori ada
    $checkKategori = mysqli_query($connect, "SELECT * FROM kategori WHERE id = $id");
    if (mysqli_num_rows($checkKategori) == 0) {
        echo "<script>alert('Data tidak ditemukan');window.location.href='../../pages/kategori/index.php';</script>";
        exit;
    }

    // Query hapus data
    $qDelete = "DELETE FROM kategori WHERE id = $id";
    $result = mysqli_query($connect, $qDelete);

    if ($result) {
        echo "<script>alert('Data Berhasil dihapus');window.location.href='../../pages/kategori/index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data: " . addslashes(mysqli_error($connect)) . "');window.location.href='../../pages/kategori/index.php';</script>";
    }
} else {
    echo "<script>alert('ID tidak ditemukan');window.location.href='../../pages/kategori/index.php';</script>";
}
?>