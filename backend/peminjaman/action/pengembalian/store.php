<?php
session_start();
include '../../app.php';

if (isset($_POST['tombol'])) {
    $peminjaman_id = escapeString($_POST['peminjaman_id']);
    $tgl_kembali = escapeString($_POST['tgl_kembali']);
    $kondisi = escapeString($_POST['kondisi']);
    $denda = escapeString($_POST['denda']);
    $keterangan = escapeString($_POST['keterangan']);

    // 1. CEK DULU apakah sudah ada pengembalian untuk peminjaman ini
    $qCekPengembalian = "SELECT id FROM pengembalian WHERE peminjaman_id = '$peminjaman_id'";
    $resultCek = mysqli_query($connect, $qCekPengembalian);
    
    if (mysqli_num_rows($resultCek) > 0) {
        $_SESSION['error'] = "Peminjaman #$peminjaman_id sudah dikembalikan sebelumnya!";
        header("Location: ../../pages/pengembalian/create.php");
        exit;
    }

    // 2. Ambil data peminjaman terlebih dahulu untuk mendapatkan barang_id dan jumlah pinjam
    $qGetPeminjaman = "SELECT barang_id, jumlah, status FROM peminjaman WHERE id = '$peminjaman_id'";
    $resultPeminjaman = mysqli_query($connect, $qGetPeminjaman);
    
    if (!$resultPeminjaman || mysqli_num_rows($resultPeminjaman) == 0) {
        $_SESSION['error'] = "Data peminjaman tidak ditemukan!";
        header("Location: ../../pages/pengembalian/create.php");
        exit;
    }
    
    $peminjaman = mysqli_fetch_assoc($resultPeminjaman);
    $barang_id = $peminjaman['barang_id'];
    $jumlah_pinjam = $peminjaman['jumlah'];
    $status_peminjaman = $peminjaman['status'];
    
    // 3. Validasi: Pastikan status peminjaman adalah 'dipinjam'
    if ($status_peminjaman != 'dipinjam') {
        $_SESSION['error'] = "Peminjaman #$peminjaman_id tidak dalam status 'dipinjam'. Status saat ini: $status_peminjaman";
        header("Location: ../../pages/pengembalian/create.php");
        exit;
    }

    // 4. Insert data pengembalian
    $qInsert = "INSERT INTO pengembalian (peminjaman_id, tgl_kembali, kondisi, denda, keterangan) 
                VALUES ('$peminjaman_id', '$tgl_kembali', '$kondisi', '$denda', '$keterangan')";

    if (mysqli_query($connect, $qInsert)) {
        // 5. Update status peminjaman menjadi 'selesai'
        $qUpdatePeminjaman = "UPDATE peminjaman SET 
                              status = 'selesai',
                              tgl_kembali_aktual = '$tgl_kembali'
                              WHERE id = '$peminjaman_id'";
        
        if (!mysqli_query($connect, $qUpdatePeminjaman)) {
            $_SESSION['error'] = "Gagal update status peminjaman: " . mysqli_error($connect);
            header("Location: ../../pages/pengembalian/create.php");
            exit;
        }
        
        // 6. Update stok barang berdasarkan kondisi
        if ($kondisi == 'baik') {
            // Jika kondisi baik: tambahkan kembali jumlah yang dipinjam ke stok
            $qUpdateBarang = "UPDATE barang SET 
                             stok = stok + $jumlah_pinjam,
                             status = 'tersedia' 
                             WHERE id = '$barang_id'";
        } elseif ($kondisi == 'rusak_ringan') {
            // Jika rusak ringan: status barang jadi 'rusak_ringan'
            $qUpdateBarang = "UPDATE barang SET 
                             status = 'rusak_ringan' 
                             WHERE id = '$barang_id'";
        } elseif ($kondisi == 'rusak_berat') {
            // Jika rusak berat: status barang jadi 'rusak_berat'
            $qUpdateBarang = "UPDATE barang SET 
                             status = 'rusak_berat' 
                             WHERE id = '$barang_id'";
        }
        
        if (isset($qUpdateBarang) && !mysqli_query($connect, $qUpdateBarang)) {
            $_SESSION['error'] = "Gagal update status barang: " . mysqli_error($connect);
            header("Location: ../../pages/pengembalian/create.php");
            exit;
        }
        
        // 7. Hitung dan simpan lama pinjam
        $qHitungLama = "UPDATE peminjaman SET 
                       lama_pinjam = DATEDIFF('$tgl_kembali', tgl_pinjam)
                       WHERE id = '$peminjaman_id'";
        mysqli_query($connect, $qHitungLama);
        
        $_SESSION['success'] = "Data pengembalian berhasil ditambahkan!";
        header("Location: ../../pages/pengembalian/index.php");
        exit;
        
    } else {
        // Tangani error duplicate entry dengan lebih spesifik
        if (mysqli_errno($connect) == 1062) { // Error code for duplicate entry
            $_SESSION['error'] = "Peminjaman #$peminjaman_id sudah dikembalikan sebelumnya (duplicate entry)!";
        } else {
            $_SESSION['error'] = "Gagal menambahkan pengembalian: " . mysqli_error($connect);
        }
        header("Location: ../../pages/pengembalian/create.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Form tidak dikirim dengan benar!";
    header("Location: ../../pages/pengembalian/create.php");
    exit;
}

// Fungsi untuk escape string
function escapeString($value) {
    global $connect;
    return mysqli_real_escape_string($connect, trim($value));
}
?>