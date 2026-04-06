<?php
include '../../app.php';

session_start();

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (isset($_POST['tombol'])) {
    $kode_barang = escapeString($_POST['kode_barang']);
    $nama_barang = escapeString($_POST['nama_barang']);
    $jenis = escapeString($_POST['jenis']);
    $merk = escapeString($_POST['merk']);
    $tahun = isset($_POST['tahun']) && !empty($_POST['tahun']) ? (int)$_POST['tahun'] : NULL;
    $jumlah = (int)$_POST['jumlah'];
    $status = escapeString($_POST['status']);
    $harga_sewa_perhari = (float)$_POST['harga_sewa_perhari'];
    $deskripsi = escapeString($_POST['deskripsi']);

    // Cek kode barang unik (kecuali untuk data ini sendiri)
    $checkKode = mysqli_query($connect, "SELECT * FROM barang WHERE kode_barang = '$kode_barang' AND id != $id");
    if (mysqli_num_rows($checkKode) > 0) {
        $_SESSION['error'] = "Kode barang sudah digunakan!";
        header("Location: ../../pages/alat/edit.php?id=$id");
        exit;
    }

    // Update data
    $qUpdate = "UPDATE barang SET 
                kode_barang = '$kode_barang', 
                nama_barang = '$nama_barang', 
                jenis = '$jenis', 
                merk = '$merk', 
                tahun = $tahun, 
                jumlah = $jumlah,
                status = '$status', 
                harga_sewa_perhari = $harga_sewa_perhari, 
                deskripsi = '$deskripsi' 
                WHERE id = $id";

    $result = mysqli_query($connect, $qUpdate);
    if ($result) {
        $_SESSION['success'] = "Data alat berat berhasil diupdate!";
        header("Location: ../../pages/alat/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal mengupdate alat berat: " . mysqli_error($connect);
        header("Location: ../../pages/alat/edit.php?id=$id");
        exit;
    }
}
?>