<?php
// show.php
include '../../app.php';

// Pastikan ID ada di URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>
        alert('ID tidak valid');
        window.location.href='../../pages/peminjaman/index.php';
    </script>";
    exit;
}

$id = intval($_GET['id']); // Convert ke integer untuk keamanan

// Query yang benar - JOIN melalui tabel barang
$query = "SELECT p.*, u.username, u.role, b.nama_barang, b.kategori_id, k.nama_kategori 
          FROM peminjaman p 
          JOIN users u ON p.user_id = u.id 
          JOIN barang b ON p.barang_id = b.id 
          JOIN kategori k ON b.kategori_id = k.id
          WHERE p.id = $id";

$result = mysqli_query($connect, $query); // PASTIKAN $connect adalah koneksi database

if (!$result) {
    die("Query error: " . mysqli_error($connect));
}

$peminjaman = mysqli_fetch_object($result);

if (!$peminjaman) {
    echo "<script>
        alert('Data peminjaman tidak ditemukan');
        window.location.href='../../pages/peminjaman/index.php';
    </script>";
    exit;
}

// Ambil data user, barang, dan kategori untuk dropdown
$users = mysqli_query($connect, "SELECT id, username FROM users WHERE role = 'peminjam' ORDER BY username");
$barang = mysqli_query($connect, "SELECT id, nama_barang, kategori_id FROM barang ORDER BY nama_barang");
$kategori = mysqli_query($connect, "SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori");
?>