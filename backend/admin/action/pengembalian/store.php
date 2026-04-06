<?php
session_start();
include '../../app.php';

// Fungsi untuk escape string
function escapeString($value) {
    global $connect;
    return mysqli_real_escape_string($connect, trim($value));
}

// FUNGSI LOGGING DISIMPAN DULU - Nonaktifkan sementara
function logActivitySimple($aksi, $deskripsi, $tipe_data = 'pengembalian', $data_id = null) {
    // KOSONGKAN DULU UNTUK MENGHINDARI ERROR
    return true;
}

if (isset($_POST['tombol'])) {
    // Ambil data dari form
    $peminjaman_id = escapeString($_POST['peminjaman_id']);
    $tgl_kembali = escapeString($_POST['tgl_kembali']);
    $kondisi = escapeString($_POST['kondisi']);
    $denda = escapeString($_POST['denda']) ?: '0';
    $keterangan = escapeString($_POST['keterangan']);
    
    // Validasi input wajib
    if (empty($peminjaman_id) || empty($tgl_kembali) || empty($kondisi)) {
        $_SESSION['error'] = "Data wajib tidak boleh kosong!";
        header("Location: ../../pages/pengembalian/create.php");
        exit;
    }
    
    // DEBUG: Tampilkan data yang diterima
    error_log("=== DEBUG PENGEMBALIAN ===");
    error_log("Peminjaman ID: " . $peminjaman_id);
    error_log("Tanggal Kembali: " . $tgl_kembali);
    error_log("Kondisi: " . $kondisi);
    error_log("Denda: " . $denda);

    // 1. CEK DULU apakah sudah ada pengembalian untuk peminjaman ini
    $qCekPengembalian = "SELECT id, tgl_kembali FROM pengembalian WHERE peminjaman_id = '$peminjaman_id'";
    $resultCek = mysqli_query($connect, $qCekPengembalian);
    
    if (mysqli_num_rows($resultCek) > 0) {
        $pengembalian = mysqli_fetch_assoc($resultCek);
        $tgl_pengembalian_sebelumnya = $pengembalian['tgl_kembali'];
        
        $_SESSION['error'] = "Peminjaman #$peminjaman_id sudah dikembalikan pada tanggal: <strong>$tgl_pengembalian_sebelumnya</strong>!";
        header("Location: ../../pages/pengembalian/create.php");
        exit;
    }

    // 2. Ambil data peminjaman dengan lebih lengkap
    $qGetPeminjaman = "SELECT 
                        p.id,
                        p.kode_peminjaman,
                        p.user_id as peminjam_user_id,
                        p.barang_id, 
                        p.jumlah, 
                        p.status,
                        p.tgl_pinjam,
                        p.tgl_kembali_rencana,
                        p.tgl_kembali_aktual,
                        p.total_harga,
                        b.kode_barang,
                        b.nama_barang,
                        b.harga_sewa_perhari,
                        b.stok,
                        b.jumlah_tersedia,
                        b.status as status_barang,
                        u.username as nama_peminjam
                    FROM peminjaman p
                    LEFT JOIN barang b ON p.barang_id = b.id
                    LEFT JOIN users u ON p.user_id = u.id
                    WHERE p.id = '$peminjaman_id'";

    $resultPeminjaman = mysqli_query($connect, $qGetPeminjaman);
    
    if (!$resultPeminjaman) {
        $_SESSION['error'] = "Error query peminjaman: " . mysqli_error($connect);
        header("Location: ../../pages/pengembalian/create.php");
        exit;
    }
    
    if (mysqli_num_rows($resultPeminjaman) == 0) {
        $_SESSION['error'] = "Data peminjaman #$peminjaman_id tidak ditemukan!";
        header("Location: ../../pages/pengembalian/create.php");
        exit;
    }
    
    $peminjaman = mysqli_fetch_assoc($resultPeminjaman);
    $barang_id = $peminjaman['barang_id'];
    $jumlah_pinjam = $peminjaman['jumlah'];
    $status_peminjaman = $peminjaman['status'];
    $tgl_kembali_aktual = $peminjaman['tgl_kembali_aktual'];
    $kode_peminjaman = $peminjaman['kode_peminjaman'];
    $nama_barang = $peminjaman['nama_barang'];
    $kode_barang = $peminjaman['kode_barang'];
    $nama_peminjam = $peminjaman['nama_peminjam'];
    $tgl_pinjam = $peminjaman['tgl_pinjam'];
    $tgl_kembali_rencana = $peminjaman['tgl_kembali_rencana'];
    $harga_sewa_perhari = $peminjaman['harga_sewa_perhari'];
    $total_harga = $peminjaman['total_harga'];
    $peminjam_user_id = $peminjaman['peminjam_user_id'];
    $status_barang = $peminjaman['status_barang'];
    $stok = $peminjaman['stok'];
    $jumlah_tersedia = $peminjaman['jumlah_tersedia'];
    
    // DEBUG: Tampilkan data peminjaman
    error_log("Status Peminjaman: " . $status_peminjaman);
    error_log("Tgl Kembali Aktual: " . $tgl_kembali_aktual);
    error_log("Status Barang: " . $status_barang);
    error_log("Stok: " . $stok);
    error_log("Jumlah Tersedia: " . $jumlah_tersedia);

    // 3. Validasi lebih ketat
    if ($status_peminjaman != 'dipinjam') {
        $current_status = htmlspecialchars($status_peminjaman);
        $_SESSION['error'] = "Peminjaman #$peminjaman_id ($kode_peminjaman) tidak dalam status 'dipinjam'. Status saat ini: <strong>$current_status</strong>";
        header("Location: ../../pages/pengembalian/create.php");
        exit;
    }

    // Cek juga apakah sudah ada tgl_kembali_aktual di tabel peminjaman
    if (!empty($tgl_kembali_aktual) && $tgl_kembali_aktual != '0000-00-00') {
        $_SESSION['error'] = "Peminjaman #$peminjaman_id sudah memiliki tanggal kembali aktual: <strong>$tgl_kembali_aktual</strong>";
        header("Location: ../../pages/pengembalian/create.php");
        exit;
    }

    // 4. Hitung keterlambatan jika ada
    $hari_telat = 0;
    if (!empty($tgl_kembali_rencana)) {
        $tgl1 = new DateTime($tgl_kembali_rencana);
        $tgl2 = new DateTime($tgl_kembali);
        
        if ($tgl2 > $tgl1) {
            $selisih = $tgl1->diff($tgl2);
            $hari_telat = $selisih->days;
        }
    }

    // 5. Hitung denda otomatis jika telat (Rp 100,000 per hari)
    $denda_otomatis = 0;
    if ($hari_telat > 0) {
        $denda_otomatis = $hari_telat * 100000;
    }

    // Jika denda dari form adalah 0, gunakan denda otomatis
    if ($denda == 0 && $denda_otomatis > 0) {
        $denda = $denda_otomatis;
    }

    // 6. Mulai transaksi
    mysqli_begin_transaction($connect);

    try {
        // 7. Insert data pengembalian
        $qInsert = "INSERT INTO pengembalian 
                    (peminjaman_id, tgl_kembali, kondisi, denda, keterangan) 
                    VALUES 
                    ('$peminjaman_id', '$tgl_kembali', '$kondisi', '$denda', '$keterangan')";

        if (!mysqli_query($connect, $qInsert)) {
            throw new Exception("Gagal insert pengembalian: " . mysqli_error($connect));
        }

        $pengembalian_id = mysqli_insert_id($connect);

        // 8. Update status peminjaman menjadi 'selesai'
        $qUpdatePeminjaman = "UPDATE peminjaman SET 
                            status = 'selesai',
                            tgl_kembali_aktual = '$tgl_kembali'
                            WHERE id = '$peminjaman_id'";
        
        if (!mysqli_query($connect, $qUpdatePeminjaman)) {
            throw new Exception("Gagal update status peminjaman: " . mysqli_error($connect));
        }

        // 9. Hitung dan simpan lama pinjam
        $qHitungLama = "UPDATE peminjaman SET 
                       lama_pinjam = DATEDIFF('$tgl_kembali', tgl_pinjam)
                       WHERE id = '$peminjaman_id'";
        
        mysqli_query($connect, $qHitungLama); // Ignore error if any

        // 10. Update stok barang berdasarkan kondisi
        $status_barang_baru = 'tersedia';
        
        switch ($kondisi) {
            case 'baik':
                // Jika kondisi baik: tambahkan kembali ke jumlah_tersedia
                $qUpdateBarang = "UPDATE barang SET 
                                 jumlah_tersedia = jumlah_tersedia + $jumlah_pinjam,
                                 status = 'tersedia' 
                                 WHERE id = '$barang_id'";
                $status_barang_baru = 'tersedia';
                break;
                
            case 'rusak_ringan':
                $qUpdateBarang = "UPDATE barang SET 
                                 status = 'rusak' 
                                 WHERE id = '$barang_id'";
                $status_barang_baru = 'rusak';
                break;
                
            case 'rusak_berat':
                $qUpdateBarang = "UPDATE barang SET 
                                 status = 'rusak_berat' 
                                 WHERE id = '$barang_id'";
                $status_barang_baru = 'rusak_berat';
                break;
                
            default:
                $qUpdateBarang = "UPDATE barang SET 
                                 jumlah_tersedia = jumlah_tersedia + $jumlah_pinjam,
                                 status = 'tersedia' 
                                 WHERE id = '$barang_id'";
                $status_barang_baru = 'tersedia';
        }

        if (!mysqli_query($connect, $qUpdateBarang)) {
            throw new Exception("Gagal update status barang: " . mysqli_error($connect));
        }

        // 11. Update total harga peminjaman jika ada denda
        if ($denda > 0) {
            $total_harga_baru = $total_harga + $denda;
            $qUpdateHarga = "UPDATE peminjaman SET 
                           total_harga = '$total_harga_baru'
                           WHERE id = '$peminjaman_id'";
            
            mysqli_query($connect, $qUpdateHarga); // Ignore error if any
        }

        // 12. Commit transaksi
        mysqli_commit($connect);

        // 13. Set success message
        $success_msg = "✅ <strong>Pengembalian Berhasil!</strong><br>" .
                      "Peminjaman: $kode_peminjaman<br>" .
                      "Barang: $nama_barang<br>" .
                      "Peminjam: $nama_peminjam<br>" .
                      "Tanggal Kembali: " . date('d/m/Y', strtotime($tgl_kembali)) . "<br>" .
                      "Kondisi: " . ucfirst(str_replace('_', ' ', $kondisi)) . "<br>";
        
        if ($denda > 0) {
            $success_msg .= "Denda: Rp " . number_format($denda, 0, ',', '.');
            if ($hari_telat > 0) {
                $success_msg .= " (Telat $hari_telat hari)";
            }
            $success_msg .= "<br>";
        }
        
        if ($status_barang_baru != 'tersedia') {
            $success_msg .= "Status Barang: " . ucfirst($status_barang_baru) . "<br>";
        }
        
        $_SESSION['success'] = $success_msg;
        
        // DEBUG: Log berhasil
        error_log("PENGEMBALIAN BERHASIL untuk peminjaman #$peminjaman_id");
        
        // 14. Redirect ke halaman index
        header("Location: ../../pages/pengembalian/index.php");
        exit;
        
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        mysqli_rollback($connect);
        
        $_SESSION['error'] = "❌ <strong>Gagal menyimpan pengembalian!</strong><br>" . 
                            $e->getMessage() . "<br>" .
                            "Silakan coba lagi atau hubungi administrator.";
        
        // DEBUG: Log error
        error_log("ERROR PENGEMBALIAN: " . $e->getMessage());
        
        header("Location: ../../pages/pengembalian/create.php");
        exit;
    }
    
} else {
    $_SESSION['error'] = "Form tidak dikirim dengan benar!";
    header("Location: ../../pages/pengembalian/create.php");
    exit;
}
?>