<?php
include '../../app.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validasi apakah form disubmit
if (isset($_POST['tombol'])) {
    // Validasi input POST
    $kode_peminjaman = isset($_POST['kode_peminjaman']) ? escapeString($_POST['kode_peminjaman']) : '';
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $barang_id = isset($_POST['barang_id']) ? (int)$_POST['barang_id'] : 0;
    $jumlah = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;
    $tgl_pinjam = isset($_POST['tgl_pinjam']) ? escapeString($_POST['tgl_pinjam']) : '';
    $tgl_kembali_rencana = isset($_POST['tgl_kembali_rencana']) ? escapeString($_POST['tgl_kembali_rencana']) : '';
    
    // Tanggal kembali aktual bisa NULL
    if (!empty($_POST['tgl_kembali_aktual'])) {
        $tgl_kembali_aktual = "'" . escapeString($_POST['tgl_kembali_aktual']) . "'";
    } else {
        $tgl_kembali_aktual = "NULL";
    }
    
    // Status peminjaman
    $status = isset($_POST['status']) ? escapeString($_POST['status']) : 'pending';
    
    // Validasi data wajib
    if (empty($kode_peminjaman) || $user_id <= 0 || $barang_id <= 0 || empty($tgl_pinjam) || empty($tgl_kembali_rencana)) {
        $_SESSION['error'] = "Semua field wajib diisi dengan benar";
        header("Location: ../../pages/peminjaman/edit.php?id=$id");
        exit;
    }
    
    // Hitung lama pinjam
    try {
        $tgl1 = new DateTime($tgl_pinjam);
        $tgl2 = new DateTime($tgl_kembali_rencana);
        $lama_pinjam = $tgl1->diff($tgl2)->days + 1;
        
        if ($lama_pinjam < 1) {
            $_SESSION['error'] = "Tanggal kembali rencana harus setelah tanggal pinjam";
            header("Location: ../../pages/peminjaman/edit.php?id=$id");
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Format tanggal tidak valid";
        header("Location: ../../pages/peminjaman/edit.php?id=$id");
        exit;
    }
    
    // Validasi kode peminjaman unik (kecuali untuk dirinya sendiri)
    $checkPeminjaman = mysqli_query($connect, "SELECT * FROM peminjaman WHERE kode_peminjaman = '$kode_peminjaman' AND id != $id");
    if (mysqli_num_rows($checkPeminjaman) > 0) {
        $_SESSION['error'] = "Kode peminjaman '$kode_peminjaman' sudah digunakan";
        header("Location: ../../pages/peminjaman/edit.php?id=$id");
        exit;
    }
    
    // Validasi stok barang
    $checkStok = mysqli_query($connect, "SELECT stok FROM barang WHERE id = $barang_id");
    if ($row = mysqli_fetch_assoc($checkStok)) {
        $stok_tersedia = $row['stok'];
        
        // Cek jumlah pinjaman sebelumnya
        $checkJumlahSebelumnya = mysqli_query($connect, "SELECT jumlah FROM peminjaman WHERE id = $id");
        $jumlah_sebelumnya = 0;
        if ($row2 = mysqli_fetch_assoc($checkJumlahSebelumnya)) {
            $jumlah_sebelumnya = $row2['jumlah'];
        }
        
        // Jika barang yang sama, hitung selisih
        $checkBarangSebelumnya = mysqli_query($connect, "SELECT barang_id FROM peminjaman WHERE id = $id");
        $row3 = mysqli_fetch_assoc($checkBarangSebelumnya);
        $barang_id_sebelumnya = $row3['barang_id'];
        
        if ($barang_id == $barang_id_sebelumnya) {
            // Barang sama, hitung perubahan
            $selisih = $jumlah - $jumlah_sebelumnya;
            if ($selisih > 0 && $selisih > $stok_tersedia) {
                $_SESSION['error'] = "Stok tidak cukup. Stok tersedia: $stok_tersedia";
                header("Location: ../../pages/peminjaman/edit.php?id=$id");
                exit;
            }
        } else {
            // Barang berbeda, cek stok baru
            if ($jumlah > $stok_tersedia) {
                $_SESSION['error'] = "Stok tidak cukup. Stok tersedia: $stok_tersedia";
                header("Location: ../../pages/peminjaman/edit.php?id=$id");
                exit;
            }
        }
    } else {
        $_SESSION['error'] = "Barang tidak ditemukan";
        header("Location: ../../pages/peminjaman/edit.php?id=$id");
        exit;
    }
    
    // Debug: Tampilkan query sebelum dijalankan
    error_log("Update Query: UPDATE peminjaman SET kode_peminjaman = '$kode_peminjaman', user_id = $user_id, barang_id = $barang_id, jumlah = $jumlah, tgl_pinjam = '$tgl_pinjam', tgl_kembali_rencana = '$tgl_kembali_rencana', tgl_kembali_aktual = $tgl_kembali_aktual, lama_pinjam = $lama_pinjam, status = '$status' WHERE id = $id");
    
    // Query update
    $qUpdate = "UPDATE peminjaman SET 
                kode_peminjaman = '$kode_peminjaman',
                user_id = $user_id,
                barang_id = $barang_id,
                jumlah = $jumlah,
                tgl_pinjam = '$tgl_pinjam',
                tgl_kembali_rencana = '$tgl_kembali_rencana',
                tgl_kembali_aktual = $tgl_kembali_aktual,
                lama_pinjam = $lama_pinjam,
                status = '$status'
                WHERE id = $id";

    $result = mysqli_query($connect, $qUpdate);
    
    if ($result) {
        // Update stok barang jika barang berubah
        if ($barang_id != $barang_id_sebelumnya) {
            // Kembalikan stok barang lama
            mysqli_query($connect, "UPDATE barang SET stok = stok + $jumlah_sebelumnya WHERE id = $barang_id_sebelumnya");
            // Kurangi stok barang baru
            mysqli_query($connect, "UPDATE barang SET stok = stok - $jumlah WHERE id = $barang_id");
        } else {
            // Update stok berdasarkan perubahan jumlah
            if ($jumlah != $jumlah_sebelumnya) {
                $selisih = $jumlah - $jumlah_sebelumnya;
                if ($selisih < 0) {
                    // Jumlah berkurang, tambah stok
                    mysqli_query($connect, "UPDATE barang SET stok = stok + abs($selisih) WHERE id = $barang_id");
                } else {
                    // Jumlah bertambah, kurangi stok
                    mysqli_query($connect, "UPDATE barang SET stok = stok - $selisih WHERE id = $barang_id");
                }
            }
        }
        
        $_SESSION['success'] = "Data peminjaman berhasil diupdate!";
        header("Location: ../../pages/peminjaman/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal mengupdate peminjaman: " . mysqli_error($connect);
        header("Location: ../../pages/peminjaman/edit.php?id=$id");
        exit;
    }
} else {
    // Jika tidak ada POST data, redirect kembali
    $_SESSION['error'] = "Form tidak dikirim dengan benar";
    header("Location: ../../pages/peminjaman/edit.php?id=$id");
    exit;
}
?>