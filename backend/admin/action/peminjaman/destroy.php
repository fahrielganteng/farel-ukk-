<?php
session_start();
include '../../app.php';

// Ambil ID dari parameter URL
$id = isset($_GET['id']) ? mysqli_real_escape_string($connect, $_GET['id']) : 0;

if ($id == 0) {
    $_SESSION['error'] = "ID tidak valid!";
    header("Location: ../../pages/peminjaman/index.php");
    exit;
}

// Cek apakah peminjaman ada
$qCek = "SELECT id, kode_peminjaman, status FROM peminjaman WHERE id = '$id'";
$resultCek = mysqli_query($connect, $qCek);

if (mysqli_num_rows($resultCek) == 0) {
    $_SESSION['error'] = "Data peminjaman tidak ditemukan!";
    header("Location: ../../pages/peminjaman/index.php");
    exit;
}

$peminjaman = mysqli_fetch_assoc($resultCek);
$kode_peminjaman = $peminjaman['kode_peminjaman'];
$status = $peminjaman['status'];

// Cek apakah ada pengembalian terkait
$qCekPengembalian = "SELECT id FROM pengembalian WHERE peminjaman_id = '$id'";
$resultCekPengembalian = mysqli_query($connect, $qCekPengembalian);
$adaPengembalian = mysqli_num_rows($resultCekPengembalian) > 0;

// Mulai transaksi
mysqli_begin_transaction($connect);

try {
    // Jika ada pengembalian terkait, hapus dulu data pengembalian
    if ($adaPengembalian) {
        // 1. Ambil data pengembalian untuk logging/restore jika perlu
        $qGetPengembalian = "SELECT barang_id, jumlah FROM peminjaman WHERE id = '$id'";
        $resultGet = mysqli_query($connect, $qGetPengembalian);
        $data = mysqli_fetch_assoc($resultGet);
        $barang_id = $data['barang_id'];
        $jumlah = $data['jumlah'];
        
        // 2. Update stok barang jika peminjaman status 'selesai' atau 'dipinjam'
        if ($status == 'selesai') {
            // Jika sudah selesai, kurangi jumlah_tersedia karena sudah dikembalikan
            $qUpdateBarang = "UPDATE barang SET 
                             jumlah_tersedia = jumlah_tersedia - $jumlah 
                             WHERE id = '$barang_id'";
        } elseif ($status == 'dipinjam') {
            // Jika masih dipinjam, tambahkan kembali ke jumlah_tersedia
            $qUpdateBarang = "UPDATE barang SET 
                             jumlah_tersedia = jumlah_tersedia + $jumlah,
                             status = 'tersedia'
                             WHERE id = '$barang_id'";
        }
        
        if (isset($qUpdateBarang) && !mysqli_query($connect, $qUpdateBarang)) {
            throw new Exception("Gagal update stok barang: " . mysqli_error($connect));
        }
        
        // 3. Hapus data pengembalian
        $qDeletePengembalian = "DELETE FROM pengembalian WHERE peminjaman_id = '$id'";
        if (!mysqli_query($connect, $qDeletePengembalian)) {
            throw new Exception("Gagal menghapus data pengembalian: " . mysqli_error($connect));
        }
    }
    
    // 4. Hapus data peminjaman
    $qDeletePeminjaman = "DELETE FROM peminjaman WHERE id = '$id'";
    if (!mysqli_query($connect, $qDeletePeminjaman)) {
        throw new Exception("Gagal menghapus data peminjaman: " . mysqli_error($connect));
    }
    
    // Commit transaksi
    mysqli_commit($connect);
    
    // Set pesan sukses
    $pesan = $adaPengembalian ? 
        "Data peminjaman <strong>$kode_peminjaman</strong> beserta data pengembalian terkait berhasil dihapus!" :
        "Data peminjaman <strong>$kode_peminjaman</strong> berhasil dihapus!";
    
    $_SESSION['success'] = $pesan;
    
} catch (Exception $e) {
    // Rollback jika ada error
    mysqli_rollback($connect);
    $_SESSION['error'] = "Gagal menghapus data: " . $e->getMessage();
}

// Redirect ke halaman index
header("Location: ../../pages/peminjaman/index.php");
exit;
?>