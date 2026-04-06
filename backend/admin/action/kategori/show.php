<?php
include '../../app.php';

// Pastikan ID ada di URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>
        alert('ID tidak valid');
        window.location.href='../../pages/kategori/index.php';
    </script>";
    exit;
}

$id = (int) $_GET['id'];

// Query ambil data dari tabel kategori
$qSelect = "SELECT * FROM kategori WHERE id = $id";
$result = mysqli_query($connect, $qSelect) or die(mysqli_error($connect));

$kategori = mysqli_fetch_object($result);

if (!$kategori) {
    echo "<script>
        alert('Data tidak ditemukan');
        window.location.href='../../pages/kategori/index.php';
    </script>";
    exit;
}
?>