<?php
include '../../app.php';

session_start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (isset($_POST['tombol'])) {
    $kode_barang = escapeString($_POST['kode_barang']);
    $nama_barang = escapeString($_POST['nama_barang']);
    $merk = escapeString($_POST['merk'] ?? '');
    $tahun = isset($_POST['tahun']) && !empty($_POST['tahun']) ? (int)$_POST['tahun'] : NULL;
    $jumlah = (int)$_POST['jumlah'];
    $status = escapeString($_POST['status']);
    $harga_sewa_perhari = (float)$_POST['harga_sewa_perhari'];
    $deskripsi = escapeString($_POST['deskripsi'] ?? '');
    $gambar_lama = escapeString($_POST['gambar_lama'] ?? '');

    // Cek kode barang unik (kecuali untuk data ini sendiri)
    $checkKode = mysqli_query($connect, "SELECT * FROM barang WHERE kode_barang = '$kode_barang' AND id != $id");
    if (mysqli_num_rows($checkKode) > 0) {
        $_SESSION['error'] = "Kode barang sudah digunakan!";
        header("Location: ../../pages/alat/edit.php?id=$id");
        exit;
    }

    // Handle file upload (new image)
    $gambar = $gambar_lama; // keep existing by default
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = dirname(__FILE__, 5) . '/storages/alat/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed) && $_FILES['gambar']['size'] <= 5 * 1024 * 1024) {
            $fileName = 'alat_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $fileName)) {
                if ($gambar_lama && file_exists($uploadDir . $gambar_lama)) {
                    unlink($uploadDir . $gambar_lama);
                }
                $gambar = $fileName;
            }
        } else {
            $_SESSION['error'] = "Format gambar tidak valid atau ukuran melebihi 5MB.";
            header("Location: ../../pages/alat/edit.php?id=$id");
            exit;
        }
    }

    $tahunVal  = $tahun  ? $tahun  : "NULL";
    $gambarEsc = escapeString($gambar);
    $gambarSQL = $gambar ? "'$gambarEsc'" : "NULL";

    // Check if jenis column exists
    $hasJenis = false;
    $colCheck = mysqli_query($connect, "SHOW COLUMNS FROM barang LIKE 'jenis'");
    if ($colCheck && mysqli_num_rows($colCheck) > 0) $hasJenis = true;

    if ($hasJenis) {
        $jenis = escapeString($_POST['jenis'] ?? '');
        $qUpdate = "UPDATE barang SET 
                    kode_barang = '$kode_barang', 
                    nama_barang = '$nama_barang', 
                    jenis = '$jenis', 
                    merk = '$merk', 
                    tahun = $tahunVal, 
                    jumlah = $jumlah,
                    status = '$status', 
                    harga_sewa_perhari = $harga_sewa_perhari, 
                    deskripsi = '$deskripsi',
                    gambar = $gambarSQL
                    WHERE id = $id";
    } else {
        $qUpdate = "UPDATE barang SET 
                    kode_barang = '$kode_barang', 
                    nama_barang = '$nama_barang', 
                    merk = '$merk', 
                    tahun = $tahunVal, 
                    jumlah = $jumlah,
                    status = '$status', 
                    harga_sewa_perhari = $harga_sewa_perhari, 
                    deskripsi = '$deskripsi',
                    gambar = $gambarSQL
                    WHERE id = $id";
    }

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