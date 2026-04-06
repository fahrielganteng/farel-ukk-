<?php
include '../../app.php';


// Pastikan ID ada di URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>
        alert('ID tidak valid');
        window.location.href='../../pages/alat/index.php';
    </script>";
    exit;
}

$id = (int) $_GET['id'];

// Query ambil data dari tabel barang
$qSelect = "SELECT * FROM barang WHERE id = $id";
$result = mysqli_query($connect, $qSelect) or die(mysqli_error($connect));

$alat = mysqli_fetch_object($result);

if (!$alat) {
    echo "<script>
        alert('Data alat berat tidak ditemukan');
        window.location.href='../../pages/alat/index.php';
    </script>";
    exit;
}
?>