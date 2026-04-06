<?php
include '../../app.php';

session_start();

if (isset($_POST['tombol'])) {
    $kode_barang = escapeString($_POST['kode_barang']);
    $nama_barang = escapeString($_POST['nama_barang']);
    $merk = escapeString($_POST['merk'] ?? '');
    $tahun = isset($_POST['tahun']) && !empty($_POST['tahun']) ? (int)$_POST['tahun'] : NULL;
    $jumlah = (int)$_POST['jumlah'];
    $jumlah_tersedia = (int)$_POST['jumlah'];
    $status = escapeString($_POST['status']);
    $harga_sewa_perhari = (float)$_POST['harga_sewa_perhari'];
    $deskripsi = escapeString($_POST['deskripsi'] ?? '');

    // Validasi kode barang unik
    $checkBarang = mysqli_query($connect, "SELECT * FROM barang WHERE kode_barang = '$kode_barang'");
    if (mysqli_num_rows($checkBarang) > 0) {
        $_SESSION['error'] = "Kode barang sudah digunakan, silahkan pilih kode lain.";
        header("Location: ../../pages/alat/create.php");
        exit;
    }

    // Handle file upload
    $gambar = '';
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
                $gambar = $fileName;
            }
        } else {
            $_SESSION['error'] = "Format gambar tidak valid atau ukuran melebihi 5MB.";
            header("Location: ../../pages/alat/create.php");
            exit;
        }
    }

    $tahunVal  = $tahun  ? $tahun  : "NULL";
    $gambarVal = $gambar ? "'" . escapeString($gambar) . "'" : "NULL";

    // Build INSERT with only columns that exist in DB
    // Check if jenis column exists
    $hasJenis = false;
    $colCheck = mysqli_query($connect, "SHOW COLUMNS FROM barang LIKE 'jenis'");
    if ($colCheck && mysqli_num_rows($colCheck) > 0) $hasJenis = true;

    if ($hasJenis) {
        $jenis = escapeString($_POST['jenis'] ?? '');
        $qInsert = "INSERT INTO barang 
                    (kode_barang, nama_barang, jenis, merk, tahun, jumlah, jumlah_tersedia, status, harga_sewa_perhari, deskripsi, gambar) 
                    VALUES 
                    ('$kode_barang', '$nama_barang', '$jenis', '$merk', $tahunVal, $jumlah, $jumlah_tersedia, '$status', $harga_sewa_perhari, '$deskripsi', $gambarVal)";
    } else {
        $qInsert = "INSERT INTO barang 
                    (kode_barang, nama_barang, merk, tahun, jumlah, jumlah_tersedia, status, harga_sewa_perhari, deskripsi, gambar) 
                    VALUES 
                    ('$kode_barang', '$nama_barang', '$merk', $tahunVal, $jumlah, $jumlah_tersedia, '$status', $harga_sewa_perhari, '$deskripsi', $gambarVal)";
    }

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