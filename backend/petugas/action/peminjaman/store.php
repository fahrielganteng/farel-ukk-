<?php
// store.php
session_start();
include '../../app.php';

if (isset($_POST['tombol'])) {
    // Ambil data dari POST
    $kode_peminjaman = isset($_POST['kode_peminjaman']) ? mysqli_real_escape_string($connect, $_POST['kode_peminjaman']) : '';
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $barang_id = isset($_POST['barang_id']) ? (int)$_POST['barang_id'] : 0;
    $jumlah = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;
    $tgl_pinjam = isset($_POST['tgl_pinjam']) ? mysqli_real_escape_string($connect, $_POST['tgl_pinjam']) : '';
    $tgl_kembali_rencana = isset($_POST['tgl_kembali_rencana']) ? mysqli_real_escape_string($connect, $_POST['tgl_kembali_rencana']) : '';
    $status = isset($_POST['status']) ? mysqli_real_escape_string($connect, $_POST['status']) : 'pending';
    $keterangan = isset($_POST['keterangan']) ? mysqli_real_escape_string($connect, $_POST['keterangan']) : '';
    
    // Validasi data wajib
    if (empty($kode_peminjaman) || $user_id <= 0 || $barang_id <= 0 || $jumlah <= 0 || empty($tgl_pinjam) || empty($tgl_kembali_rencana)) {
        $_SESSION['error'] = "Semua field wajib diisi dengan benar";
        header("Location: ../../pages/peminjaman/create.php");
        exit;
    }
    
    // Validasi kode peminjaman unik
    $checkPeminjaman = mysqli_query($connect, "SELECT * FROM peminjaman WHERE kode_peminjaman = '$kode_peminjaman'");
    if (mysqli_num_rows($checkPeminjaman) > 0) {
        $_SESSION['error'] = "Kode peminjaman '$kode_peminjaman' sudah digunakan";
        header("Location: ../../pages/peminjaman/create.php");
        exit;
    }
    
    // Validasi stok barang
    $checkStok = mysqli_query($connect, "SELECT jumlah_tersedia FROM barang WHERE id = $barang_id");
    if ($row = mysqli_fetch_assoc($checkStok)) {
        $stok_tersedia = $row['jumlah_tersedia'];
        if ($jumlah > $stok_tersedia) {
            $_SESSION['error'] = "Stok tidak cukup. Stok tersedia: $stok_tersedia";
            header("Location: ../../pages/peminjaman/create.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Barang tidak ditemukan";
        header("Location: ../../pages/peminjaman/create.php");
        exit;
    }
    
    // Hitung lama pinjam
    try {
        $tgl1 = new DateTime($tgl_pinjam);
        $tgl2 = new DateTime($tgl_kembali_rencana);
        $diff = $tgl1->diff($tgl2);
        $lama_pinjam = $diff->days;
        
        if ($lama_pinjam < 1) {
            $_SESSION['error'] = "Tanggal kembali rencana harus setelah tanggal pinjam";
            header("Location: ../../pages/peminjaman/create.php");
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Format tanggal tidak valid";
        header("Location: ../../pages/peminjaman/create.php");
        exit;
    }
    
    // Hitung total harga
    $harga_barang = 0;
    $checkHarga = mysqli_query($connect, "SELECT harga_sewa_perhari FROM barang WHERE id = $barang_id");
    if ($row = mysqli_fetch_assoc($checkHarga)) {
        $harga_barang = $row['harga_sewa_perhari'];
    }
    $total_harga = $lama_pinjam * $jumlah * $harga_barang;
    
    // Query insert tanpa menyebutkan kolom id (biarkan auto increment)
    $qInsert = "INSERT INTO peminjaman 
                (kode_peminjaman, user_id, barang_id, jumlah, tgl_pinjam, tgl_kembali_rencana, lama_pinjam, total_harga, status, keterangan) 
                VALUES 
                ('$kode_peminjaman', $user_id, $barang_id, $jumlah, '$tgl_pinjam', '$tgl_kembali_rencana', $lama_pinjam, $total_harga, '$status', '$keterangan')";

    $result = mysqli_query($connect, $qInsert);
    
    if ($result) {
        // Update stok barang jika status langsung dipinjam
        if ($status == 'dipinjam') {
            mysqli_query($connect, "UPDATE barang SET jumlah_tersedia = jumlah_tersedia - $jumlah WHERE id = $barang_id");
        }
        
        $_SESSION['success'] = "Peminjaman berhasil ditambahkan!";
        header("Location: ../../pages/peminjaman/index.php");
        exit;
    } else {
        // Debug error lebih detail
        $_SESSION['error'] = "Gagal menambahkan peminjaman: " . mysqli_error($connect) . 
                            "<br>Query: " . $qInsert;
        header("Location: ../../pages/peminjaman/create.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Form tidak dikirim dengan benar";
    header("Location: ../../pages/peminjaman/create.php");
    exit;
}
?>