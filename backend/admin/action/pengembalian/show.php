<?php
include '../../app.php';

// Pastikan ID ada di URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>
        alert('ID tidak valid');
        window.location.href='../../pages/pengembalian/index.php';
    </script>";
    exit;
}

$id = (int) $_GET['id'];

// PERBAIKAN QUERY: Sesuaikan dengan struktur database alat berat
$qSelect = "SELECT 
                pb.*, 
                p.kode_peminjaman,
                p.user_id,
                p.barang_id,
                p.jumlah as jumlah_pinjam,
                p.tgl_pinjam,
                p.tgl_kembali_rencana,
                p.tgl_kembali_aktual,
                p.status as status_peminjaman,
                p.total_harga,
                p.keterangan as keterangan_peminjaman,
                u.username as nama_peminjam,
                b.nama_barang,
                b.kode_barang,
                b.merk,
                b.harga_sewa_perhari
            FROM pengembalian pb
            LEFT JOIN peminjaman p ON pb.peminjaman_id = p.id
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN barang b ON p.barang_id = b.id
            WHERE pb.id = $id";
            
$result = mysqli_query($connect, $qSelect);

if (!$result) {
    die("Query error: " . mysqli_error($connect));
}

$pengembalian = mysqli_fetch_object($result);

if (!$pengembalian) {
    echo "<script>
        alert('Data tidak ditemukan');
        window.location.href='../../pages/pengembalian/index.php';
    </script>";
    exit;
}
?>