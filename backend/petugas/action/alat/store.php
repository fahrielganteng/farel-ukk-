<?php
include '../../app.php';

session_start();

if (isset($_POST['tombol'])) {
    $kode_barang = escapeString($_POST['kode_barang']);
    $nama_barang = escapeString($_POST['nama_barang']);
    $jenis = escapeString($_POST['jenis']);
    $merk = escapeString($_POST['merk']);
    $tahun = isset($_POST['tahun']) && !empty($_POST['tahun']) ? (int)$_POST['tahun'] : NULL;
    $jumlah = (int)$_POST['jumlah'];
    $jumlah_tersedia = (int)$_POST['jumlah'];
    $status = escapeString($_POST['status']);
    $harga_sewa_perhari = (float)$_POST['harga_sewa_perhari'];
    $deskripsi = escapeString($_POST['deskripsi']);

    // Validasi kode barang unik
    $checkBarang = mysqli_query($connect, "SELECT * FROM barang WHERE kode_barang = '$kode_barang'");
    if (mysqli_num_rows($checkBarang) > 0) {
        $_SESSION['error'] = "Kode barang sudah digunakan, silahkan pilih kode lain.";
        header("Location: ../../pages/alat/create.php");
        exit;
    }

    // Query insert data alat berat
    $qInsert = "INSERT INTO barang 
                (kode_barang, nama_barang, jenis, merk, tahun, jumlah, jumlah_tersedia, status, harga_sewa_perhari, deskripsi) 
                VALUES 
                ('$kode_barang', '$nama_barang', '$jenis', '$merk', $tahun, $jumlah, $jumlah_tersedia, '$status', $harga_sewa_perhari, '$deskripsi')";

    if (mysqli_query($connect, $qInsert)) {
        $_SESSION['success'] = "Alat berat berhasil ditambahkan!";
        header("Location: ../../pages/alat/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal menambahkan alat berat: " . mysqli_error($connect);
        header("Location: ../../pages/alat/create.php");
        exit;
    }
}
?>