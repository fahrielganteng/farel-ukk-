<?php
// update.php
session_start();
include '../../app.php';

if (isset($_POST['tombol'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $barang_id = isset($_POST['barang_id']) ? (int)$_POST['barang_id'] : 0;
    $jumlah = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;
    $tgl_pinjam = isset($_POST['tgl_pinjam']) ? mysqli_real_escape_string($connect, $_POST['tgl_pinjam']) : '';
    $tgl_kembali_rencana = isset($_POST['tgl_kembali_rencana']) ? mysqli_real_escape_string($connect, $_POST['tgl_kembali_rencana']) : '';
    $status = isset($_POST['status']) ? mysqli_real_escape_string($connect, $_POST['status']) : 'pending';
    $keterangan = isset($_POST['keterangan']) ? mysqli_real_escape_string($connect, $_POST['keterangan']) : '';
    
    // Validasi
    if ($id <= 0) {
        $_SESSION['error'] = "ID peminjaman tidak valid";
        header("Location: ../../pages/peminjaman/edit.php?id=$id");
        exit;
    }
    
    if ($user_id <= 0 || $barang_id <= 0 || $jumlah <= 0 || empty($tgl_pinjam) || empty($tgl_kembali_rencana)) {
        $_SESSION['error'] = "Semua field wajib diisi dengan benar";
        header("Location: ../../pages/peminjaman/edit.php?id=$id");
        exit;
    }
    
    // Get old data for comparison
    $old_query = "SELECT barang_id, jumlah, status, tgl_pinjam, tgl_kembali_rencana FROM peminjaman WHERE id = $id";
    $old_result = mysqli_query($connect, $old_query);
    
    if (!$old_result || mysqli_num_rows($old_result) == 0) {
        $_SESSION['error'] = "Data peminjaman tidak ditemukan";
        header("Location: ../../pages/peminjaman/edit.php?id=$id");
        exit;
    }
    
    $old_data = mysqli_fetch_assoc($old_result);
    $old_barang_id = $old_data['barang_id'] ?? 0;
    $old_jumlah = $old_data['jumlah'] ?? 0;
    $old_status = $old_data['status'] ?? 'pending';
    
    // Validasi status yang diizinkan untuk update
    $allowed_statuses = ['pending', 'disetujui', 'ditolak', 'dipinjam', 'selesai'];
    if (!in_array($status, $allowed_statuses)) {
        $_SESSION['error'] = "Status '$status' tidak valid. Status yang diizinkan: " . implode(', ', $allowed_statuses);
        header("Location: ../../pages/peminjaman/edit.php?id=$id");
        exit;
    }
    
    // Stok validation hanya untuk status yang mengurangi stok
    $statuses_that_reduce_stock = ['dipinjam'];
    $new_status_reduces_stock = in_array($status, $statuses_that_reduce_stock);
    $old_status_reduces_stock = in_array($old_status, $statuses_that_reduce_stock);
    
    // Jika barang berubah atau jumlah berubah
    if ($barang_id != $old_barang_id || $jumlah != $old_jumlah || $status != $old_status) {
        $checkStok = mysqli_query($connect, "SELECT stok, jumlah_tersedia FROM barang WHERE id = $barang_id");
        
        if (!$checkStok) {
            $_SESSION['error'] = "Error checking stock: " . mysqli_error($connect);
            header("Location: ../../pages/peminjaman/edit.php?id=$id");
            exit;
        }
        
        if (mysqli_num_rows($checkStok) > 0) {
            $row = mysqli_fetch_assoc($checkStok);
            $stok_total = $row['stok'];
            $stok_tersedia = $row['jumlah_tersedia'] ?? $row['stok'];
            
            // Hitung stok yang benar berdasarkan berbagai skenario
            if ($barang_id == $old_barang_id && $old_status_reduces_stock) {
                // Jika barang sama dan status lama mengurangi stok, tambahkan kembali stok lama
                $stok_tersedia += $old_jumlah;
            }
            
            // Cek jika status baru akan mengurangi stok
            if ($new_status_reduces_stock && $jumlah > $stok_tersedia) {
                $_SESSION['error'] = "Stok tidak cukup. Stok tersedia: $stok_tersedia, Jumlah diminta: $jumlah";
                header("Location: ../../pages/peminjaman/edit.php?id=$id");
                exit;
            }
        } else {
            $_SESSION['error'] = "Barang tidak ditemukan";
            header("Location: ../../pages/peminjaman/edit.php?id=$id");
            exit;
        }
    }
    
    // Hitung lama pinjam
    try {
        $tgl1 = new DateTime($tgl_pinjam);
        $tgl2 = new DateTime($tgl_kembali_rencana);
        
        // Validasi tanggal
        if ($tgl1 > $tgl2) {
            $_SESSION['error'] = "Tanggal kembali rencana harus setelah tanggal pinjam";
            header("Location: ../../pages/peminjaman/edit.php?id=$id");
            exit;
        }
        
        $diff = $tgl1->diff($tgl2);
        $lama_pinjam = $diff->days;
        
        if ($lama_pinjam < 1) {
            $lama_pinjam = 1; // Minimum 1 hari
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Format tanggal tidak valid: " . $e->getMessage();
        header("Location: ../../pages/peminjaman/edit.php?id=$id");
        exit;
    }
    
    // Hitung total harga
    $harga_barang = 0;
    $checkHarga = mysqli_query($connect, "SELECT harga_sewa_perhari FROM barang WHERE id = $barang_id");
    if ($checkHarga && mysqli_num_rows($checkHarga) > 0) {
        $row = mysqli_fetch_assoc($checkHarga);
        $harga_barang = $row['harga_sewa_perhari'];
    }
    $total_harga = $lama_pinjam * $jumlah * $harga_barang;
    
    // Mulai transaction
    mysqli_begin_transaction($connect);
    
    try {
        // QUERY UPDATE
        $stmt = $connect->prepare("UPDATE peminjaman SET 
                    user_id = ?, 
                    barang_id = ?, 
                    jumlah = ?, 
                    tgl_pinjam = ?, 
                    tgl_kembali_rencana = ?, 
                    lama_pinjam = ?, 
                    total_harga = ?, 
                    status = ?, 
                    keterangan = ?,
                    updated_at = NOW()
                    WHERE id = ?");
        
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $connect->error);
        }
        
        $stmt->bind_param("iiissddssi", 
            $user_id, 
            $barang_id, 
            $jumlah, 
            $tgl_pinjam, 
            $tgl_kembali_rencana, 
            $lama_pinjam, 
            $total_harga, 
            $status, 
            $keterangan,
            $id
        );
        
        $result = $stmt->execute();
        
        if (!$result) {
            throw new Exception("Gagal memperbarui peminjaman: " . $stmt->error);
        }
        
        // ========== LOGIKA UPDATE STOK BERDASARKAN PERUBAHAN STATUS ==========
        
        // SKENARIO 1: Barang berubah
        if ($barang_id != $old_barang_id) {
            // Kembalikan stok barang lama jika status lama mengurangi stok
            if ($old_status_reduces_stock) {
                mysqli_query($connect, "UPDATE barang SET jumlah_tersedia = jumlah_tersedia + $old_jumlah WHERE id = $old_barang_id");
            }
            
            // Kurangi stok barang baru jika status baru mengurangi stok
            if ($new_status_reduces_stock) {
                mysqli_query($connect, "UPDATE barang SET jumlah_tersedia = jumlah_tersedia - $jumlah WHERE id = $barang_id");
            }
        } 
        // SKENARIO 2: Barang sama tapi jumlah berubah
        elseif ($jumlah != $old_jumlah) {
            $difference = $old_jumlah - $jumlah;
            
            if ($old_status_reduces_stock && $new_status_reduces_stock) {
                // Status tetap mengurangi stok, hanya adjust jumlah
                mysqli_query($connect, "UPDATE barang SET jumlah_tersedia = jumlah_tersedia + $difference WHERE id = $barang_id");
            } elseif ($old_status_reduces_stock && !$new_status_reduces_stock) {
                // Dari mengurangi stok menjadi tidak mengurangi stok
                mysqli_query($connect, "UPDATE barang SET jumlah_tersedia = jumlah_tersedia + $old_jumlah WHERE id = $barang_id");
            } elseif (!$old_status_reduces_stock && $new_status_reduces_stock) {
                // Dari tidak mengurangi stok menjadi mengurangi stok
                mysqli_query($connect, "UPDATE barang SET jumlah_tersedia = jumlah_tersedia - $jumlah WHERE id = $barang_id");
            }
        }
        // SKENARIO 3: Barang dan jumlah sama, hanya status berubah
        elseif ($status != $old_status) {
            if ($old_status_reduces_stock && !$new_status_reduces_stock) {
                // Dari status yang mengurangi stok ke status yang tidak mengurangi stok
                mysqli_query($connect, "UPDATE barang SET jumlah_tersedia = jumlah_tersedia + $jumlah WHERE id = $barang_id");
            } elseif (!$old_status_reduces_stock && $new_status_reduces_stock) {
                // Dari status yang tidak mengurangi stok ke status yang mengurangi stok
                mysqli_query($connect, "UPDATE barang SET jumlah_tersedia = jumlah_tersedia - $jumlah WHERE id = $barang_id");
            }
        }
        
        // ========== LOGIKA KHUSUS UNTUK STATUS SELESAI ==========
        // Jika status diubah menjadi 'selesai', cek apakah ada pengembalian
        if ($status == 'selesai' && $old_status != 'selesai') {
            // Cek apakah sudah ada data pengembalian
            $checkPengembalian = mysqli_query($connect, "SELECT id FROM pengembalian WHERE peminjaman_id = $id");
            if (mysqli_num_rows($checkPengembalian) == 0) {
                // Jika belum ada pengembalian, tambahkan tanggal kembali aktual
                mysqli_query($connect, "UPDATE peminjaman SET tgl_kembali_aktual = NOW() WHERE id = $id");
                
                // Tambah stok jika belum dikurangi (jika status sebelumnya bukan dipinjam)
                if (!$old_status_reduces_stock) {
                    mysqli_query($connect, "UPDATE barang SET jumlah_tersedia = jumlah_tersedia + $jumlah WHERE id = $barang_id");
                }
            }
        }
        
        // ========== LOGIKA KHUSUS UNTUK STATUS TERLAMBAT ==========
        // Cek apakah peminjaman terlambat (hanya jika status adalah dipinjam)
        if ($status == 'dipinjam') {
            $today = date('Y-m-d');
            if ($tgl_kembali_rencana < $today) {
                // Otomatis ubah status menjadi terlambat
                $status = 'terlambat';
                mysqli_query($connect, "UPDATE peminjaman SET status = 'terlambat' WHERE id = $id");
            }
        }
        
        // Commit transaction
        mysqli_commit($connect);
        
        // Log aktivitas
        $log_message = "Update peminjaman #$id: Status '$old_status' -> '$status'";
        if ($barang_id != $old_barang_id) {
            $log_message .= ", Barang $old_barang_id -> $barang_id";
        }
        if ($jumlah != $old_jumlah) {
            $log_message .= ", Jumlah $old_jumlah -> $jumlah";
        }
        
        $_SESSION['success'] = "Peminjaman berhasil diperbarui! " . $log_message;
        header("Location: ../../pages/peminjaman/index.php");
        exit;
        
    } catch (Exception $e) {
        // Rollback transaction jika ada error
        mysqli_rollback($connect);
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../../pages/peminjaman/edit.php?id=$id");
        exit;
    }
} else {
    $_SESSION['error'] = "Form tidak dikirim dengan benar";
    header("Location: ../../pages/peminjaman/edit.php");
    exit;
}
?>