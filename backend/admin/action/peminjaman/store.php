<?php
session_start();
include '../../app.php';

// Fungsi untuk escape string
function escapeString($value) {
    global $connect;
    return mysqli_real_escape_string($connect, trim($value));
}

// Fungsi untuk generate kode peminjaman
function generateKodePeminjaman() {
    global $connect;
    
    // Format: PMJ-YYYYMMDD-XXX
    $prefix = "PMJ";
    $date = date("Ymd");
    
    // Cek nomor urut hari ini
    $qCek = "SELECT COUNT(*) as total FROM peminjaman WHERE DATE(created_at) = CURDATE()";
    $result = mysqli_query($connect, $qCek);
    $row = mysqli_fetch_assoc($result);
    $seq = $row['total'] + 1;
    
    // Format sequence dengan leading zeros
    $sequence = str_pad($seq, 3, '0', STR_PAD_LEFT);
    
    return $prefix . "-" . $date . "-" . $sequence;
}

// Fungsi untuk cek apakah kolom id AUTO_INCREMENT
function isAutoIncrement($table, $column = 'id') {
    global $connect;
    
    $query = "SHOW COLUMNS FROM $table WHERE Field = '$column'";
    $result = mysqli_query($connect, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $columnInfo = mysqli_fetch_assoc($result);
        return strpos($columnInfo['Extra'], 'auto_increment') !== false;
    }
    
    return false;
}

// Fungsi untuk mendapatkan ID baru
function getNextId($table) {
    global $connect;
    
    $query = "SELECT MAX(id) as max_id FROM $table";
    $result = mysqli_query($connect, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return ($row['max_id'] ?? 0) + 1;
    }
    
    return 1;
}

if (isset($_POST['tombol'])) {
    // Ambil data dari form
    $user_id = escapeString($_POST['user_id']);
    $barang_id = escapeString($_POST['barang_id']);
    $jumlah = escapeString($_POST['jumlah']);
    $tgl_pinjam = escapeString($_POST['tgl_pinjam']);
    $tgl_kembali_rencana = escapeString($_POST['tgl_kembali_rencana']);
    $keterangan = escapeString($_POST['keterangan'] ?? '');
    
    // Validasi input
    if (empty($user_id) || empty($barang_id) || empty($jumlah) || empty($tgl_pinjam) || empty($tgl_kembali_rencana)) {
        $_SESSION['error'] = "Semua field wajib diisi!";
        header("Location: ../../pages/peminjaman/create.php");
        exit;
    }
    
    // Validasi jumlah
    if ($jumlah <= 0) {
        $_SESSION['error'] = "Jumlah harus lebih dari 0!";
        header("Location: ../../pages/peminjaman/create.php");
        exit;
    }
    
    // Validasi tanggal
    if (strtotime($tgl_pinjam) > strtotime($tgl_kembali_rencana)) {
        $_SESSION['error'] = "Tanggal kembali rencana tidak boleh sebelum tanggal pinjam!";
        header("Location: ../../pages/peminjaman/create.php");
        exit;
    }
    
    // 1. Cek stok barang
    $qCekBarang = "SELECT 
                    id, 
                    nama_barang, 
                    stok, 
                    jumlah_tersedia, 
                    status,
                    harga_sewa_perhari
                  FROM barang 
                  WHERE id = '$barang_id'";
                  
    $resultBarang = mysqli_query($connect, $qCekBarang);
    
    if (mysqli_num_rows($resultBarang) == 0) {
        $_SESSION['error'] = "Barang tidak ditemukan!";
        header("Location: ../../pages/peminjaman/create.php");
        exit;
    }
    
    $barang = mysqli_fetch_assoc($resultBarang);
    $nama_barang = $barang['nama_barang'];
    $stok = $barang['stok'];
    $jumlah_tersedia = $barang['jumlah_tersedia'];
    $status_barang = $barang['status'];
    $harga_sewa_perhari = $barang['harga_sewa_perhari'];
    
    // Cek apakah barang tersedia
    if ($status_barang != 'tersedia') {
        $_SESSION['error'] = "Barang <strong>$nama_barang</strong> tidak tersedia. Status: " . ucfirst($status_barang);
        header("Location: ../../pages/peminjaman/create.php");
        exit;
    }
    
    // Cek jumlah tersedia
    if ($jumlah > $jumlah_tersedia) {
        $_SESSION['error'] = "Stok tidak mencukupi! Tersedia: $jumlah_tersedia, Permintaan: $jumlah";
        header("Location: ../../pages/peminjaman/create.php");
        exit;
    }
    
    // 2. Generate kode peminjaman
    $kode_peminjaman = generateKodePeminjaman();
    
    // 3. Hitung lama pinjam
    $tgl1 = new DateTime($tgl_pinjam);
    $tgl2 = new DateTime($tgl_kembali_rencana);
    $lama_pinjam = $tgl1->diff($tgl2)->days;
    
    // 4. Hitung total harga
    $total_harga = $lama_pinjam * $harga_sewa_perhari * $jumlah;
    
    // 5. Cek apakah id AUTO_INCREMENT
    $isAutoInc = isAutoIncrement('peminjaman');
    
    // 6. Mulai transaksi
    mysqli_begin_transaction($connect);
    
    try {
        // JIKA AUTO_INCREMENT
        if ($isAutoInc) {
            $qInsert = "INSERT INTO peminjaman (
                            kode_peminjaman,
                            user_id,
                            barang_id,
                            jumlah,
                            tgl_pinjam,
                            tgl_kembali_rencana,
                            lama_pinjam,
                            status,
                            total_harga,
                            keterangan
                        ) VALUES (
                            '$kode_peminjaman',
                            '$user_id',
                            '$barang_id',
                            '$jumlah',
                            '$tgl_pinjam',
                            '$tgl_kembali_rencana',
                            '$lama_pinjam',
                            'pending',
                            '$total_harga',
                            '$keterangan'
                        )";
        } 
        // JIKA TIDAK AUTO_INCREMENT
        else {
            $new_id = getNextId('peminjaman');
            
            $qInsert = "INSERT INTO peminjaman (
                            id,
                            kode_peminjaman,
                            user_id,
                            barang_id,
                            jumlah,
                            tgl_pinjam,
                            tgl_kembali_rencana,
                            lama_pinjam,
                            status,
                            total_harga,
                            keterangan
                        ) VALUES (
                            '$new_id',
                            '$kode_peminjaman',
                            '$user_id',
                            '$barang_id',
                            '$jumlah',
                            '$tgl_pinjam',
                            '$tgl_kembali_rencana',
                            '$lama_pinjam',
                            'pending',
                            '$total_harga',
                            '$keterangan'
                        )";
        }
        
        // Debug: Tampilkan query untuk testing
        // error_log("Query INSERT: " . $qInsert);
        
        if (!mysqli_query($connect, $qInsert)) {
            throw new Exception("Gagal menyimpan peminjaman: " . mysqli_error($connect));
        }
        
        $peminjaman_id = mysqli_insert_id($connect);
        
        // 7. Update stok barang (kurangi jumlah_tersedia)
        $qUpdateBarang = "UPDATE barang SET 
                         jumlah_tersedia = jumlah_tersedia - $jumlah
                         WHERE id = '$barang_id'";
        
        if (!mysqli_query($connect, $qUpdateBarang)) {
            throw new Exception("Gagal update stok barang: " . mysqli_error($connect));
        }
        
        // 8. Commit transaksi
        mysqli_commit($connect);
        
        // 9. Set success message
        $success_msg = "✅ <strong>Peminjaman Berhasil!</strong><br>" .
                      "Kode: <strong>$kode_peminjaman</strong><br>" .
                      "Barang: $nama_barang<br>" .
                      "Jumlah: $jumlah<br>" .
                      "Tanggal Pinjam: " . date('d/m/Y', strtotime($tgl_pinjam)) . "<br>" .
                      "Rencana Kembali: " . date('d/m/Y', strtotime($tgl_kembali_rencana)) . "<br>" .
                      "Lama Pinjam: $lama_pinjam hari<br>" .
                      "Total Harga: Rp " . number_format($total_harga, 0, ',', '.') . "<br>" .
                      "Status: <span class='badge bg-warning'>Pending</span>";
        
        $_SESSION['success'] = $success_msg;
        
        // 10. Redirect ke halaman index
        header("Location: ../../pages/peminjaman/index.php");
        exit;
        
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        mysqli_rollback($connect);
        
        // Tampilkan error yang lebih spesifik
        $error_msg = "❌ <strong>Gagal menyimpan peminjaman!</strong><br>" . 
                    $e->getMessage() . "<br><br>" .
                    "<strong>Kemungkinan penyebab:</strong><br>" .
                    "1. Kolom 'id' tidak memiliki AUTO_INCREMENT<br>" .
                    "2. Tidak ada nilai default untuk kolom 'id'<br>" .
                    "3. Struktur tabel tidak sesuai<br><br>" .
                    "<strong>Solusi:</strong><br>" .
                    "1. Jalankan query: ALTER TABLE peminjaman MODIFY id INT AUTO_INCREMENT<br>" .
                    "2. Atau hubungi administrator database";
        
        $_SESSION['error'] = $error_msg;
        
        header("Location: ../../pages/peminjaman/create.php");
        exit;
    }
    
} else {
    $_SESSION['error'] = "Form tidak dikirim dengan benar!";
    header("Location: ../../pages/peminjaman/create.php");
    exit;
}
?>