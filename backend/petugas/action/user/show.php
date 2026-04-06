<?php
include '../../app.php';

// Pastikan ID ada di URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>
        alert('ID tidak valid');
        window.location.href='../../pages/user/index.php';
    </script>";
    exit;
}

$id = (int) $_GET['id'];

// Query ambil data dari tabel users
$qSelect = "SELECT * FROM users WHERE id = $id AND role IN ('admin', 'petugas')";
$result = mysqli_query($connect, $qSelect) or die(mysqli_error($connect));

$user = mysqli_fetch_object($result);

if (!$user) {
    echo "<script>
        alert('Data tidak ditemukan');
        window.location.href='../../pages/user/index.php';
    </script>";
    exit;
}