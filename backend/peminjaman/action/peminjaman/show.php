<?php
include '../../app.php';

// Pastikan ID ada di URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>
        alert('ID tidak valid');
        window.location.href='../../pages/peminjaman/index.php';
    </script>";
    exit;
}

$id = (int) $_GET['id'];

// Query ambil data peminjaman dengan join ke tabel users dan barang
$qSelect = "SELECT p.*, u.username, b.nama_barang 
            FROM peminjaman p 
            LEFT JOIN users u ON p.user_id = u.id 
            LEFT JOIN barang b ON p.barang_id = b.id 
            WHERE p.id = $id";
$result = mysqli_query($connect, $qSelect) or die(mysqli_error($connect));

$peminjaman = mysqli_fetch_object($result);

if (!$peminjaman) {
    echo "<script>
        alert('Data peminjaman tidak ditemukan');
        window.location.href='../../pages/peminjaman/index.php';
    </script>";
    exit;
}

// Ambil data user dan barang untuk dropdown
$users = mysqli_query($connect, "SELECT id, username FROM users");
$barang = mysqli_query($connect, "SELECT id, nama_barang FROM barang");
?>