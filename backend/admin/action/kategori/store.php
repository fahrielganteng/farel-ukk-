<?php
include '../../app.php';

if (isset($_POST['tombol'])) {
    $nama_kategori = escapeString($_POST['nama_kategori']);
    $deskripsi = escapeString($_POST['deskripsi']);

    // Validasi nama kategori unik
    $checkKategori = mysqli_query($connect, "SELECT * FROM kategori WHERE nama_kategori = '$nama_kategori'");
    if (mysqli_num_rows($checkKategori) > 0) {
        $_SESSION['error'] = "Kategori sudah ada, silahkan pilih nama lain.";
        header("Location: ../../pages/kategori/create.php");
        exit;
    }

    // Query insert
    $qInsert = "INSERT INTO kategori (nama_kategori, deskripsi) 
                VALUES ('$nama_kategori', '$deskripsi')";

    if (mysqli_query($connect, $qInsert)) {
        $_SESSION['success'] = "Kategori berhasil ditambahkan!";
        header("Location: ../../pages/kategori/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal menambahkan kategori: " . mysqli_error($connect);
        header("Location: ../../pages/kategori/create.php");
        exit;
    }
}
?>