<?php
include '../../app.php';

session_start();

if (!empty($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Cek apakah alat berat ada
    $checkAlat = mysqli_query($connect, "SELECT * FROM barang WHERE id = $id");
    if (mysqli_num_rows($checkAlat) == 0) {
        $_SESSION['error'] = "Data alat berat tidak ditemukan";
        header("Location: ../../pages/alat/index.php");
        exit;
    }

    // Cek apakah alat sedang dipinjam
    $checkPinjam = mysqli_query($connect, "SELECT * FROM peminjaman WHERE barang_id = $id AND status IN ('dipinjam', 'disetujui')");
    if (mysqli_num_rows($checkPinjam) > 0) {
        $_SESSION['error'] = "Alat berat sedang dipinjam, tidak dapat dihapus!";
        header("Location: ../../pages/alat/index.php");
        exit;
    }

    // Query hapus data
    $qDelete = "DELETE FROM barang WHERE id = $id";
    $result = mysqli_query($connect, $qDelete);

    if ($result) {
        $_SESSION['success'] = "Alat berat berhasil dihapus!";
        header("Location: ../../pages/alat/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal menghapus alat berat: " . addslashes(mysqli_error($connect));
        header("Location: ../../pages/alat/index.php");
        exit;
    }
} else {
    $_SESSION['error'] = "ID tidak ditemukan";
    header("Location: ../../pages/alat/index.php");
    exit;
}
?>