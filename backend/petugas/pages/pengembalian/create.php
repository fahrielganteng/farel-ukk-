<?php
// AKTIFKAN ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session_start() di awal
session_start();

include '../../partials/header.php'; 
$page = 'pengembalian'; 
include '../../partials/sidebar.php'; 

// Include koneksi untuk mendapatkan data peminjaman
include '../../app.php';

// PERBAIKAN QUERY: Ambil peminjaman yang statusnya 'dipinjam' dan BELUM ada di tabel pengembalian
// Di file create.php, perbaiki query menjadi:
$qPeminjaman = "SELECT 
                    p.id, 
                    p.kode_peminjaman,
                    p.user_id,
                    p.barang_id,
                    p.tgl_pinjam,
                    p.tgl_kembali_rencana,
                    p.tgl_kembali_aktual,
                    p.jumlah as jumlah_pinjam,
                    p.status as status_peminjaman,
                    u.username as nama_peminjam,
                    b.nama_barang,
                    b.kode_barang,
                    b.stok
                FROM peminjaman p 
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN barang b ON p.barang_id = b.id 
                WHERE p.status = 'dipinjam' 
                AND NOT EXISTS (
                    SELECT 1 FROM pengembalian pb 
                    WHERE pb.peminjaman_id = p.id
                )
                ORDER BY p.tgl_kembali_rencana ASC";
                
$resultPeminjaman = mysqli_query($connect, $qPeminjaman);
$peminjamanList = [];

if ($resultPeminjaman) {
    while ($row = mysqli_fetch_assoc($resultPeminjaman)) {
        $peminjamanList[] = $row;
    }
} else {
    echo "Query error: " . mysqli_error($connect);
    $peminjamanList = [];
}

$totalPeminjamanAktif = count($peminjamanList);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengembalian - Admin Panel</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* ===== GLOBAL STYLING ===== */
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Sidebar adjustment */
        #main {
            margin-left: 260px;
            margin-top: 70px;
            padding: 25px;
            width: calc(100% - 260px);
            min-height: calc(100vh - 70px);
            transition: all 0.3s ease;
            background-color: #f8f9fc;
        }

        @media (max-width: 768px) {
            #main {
                margin-left: 0;
                width: 100%;
                padding: 15px;
            }
        }

        /* Card styling */
        .main-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e3e6f0;
            overflow: hidden;
        }

        .card-header-custom {
            background: #ffffff;
            border-bottom: 2px solid #f0f2f5;
            padding: 20px 30px;
        }

        .card-body-custom {
            padding: 30px;
        }

        /* Header styling */
        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #4e73df;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title i {
            font-size: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Button styling */
        .btn-add {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-add:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-back {
            background: #6c757d;
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: #5a6268;
            color: white;
        }

        .btn-submit {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(67, 233, 123, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(67, 233, 123, 0.4);
            color: white;
        }

        /* Alert styling */
        .alert-container {
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 25px;
            border: none;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .alert-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border: none;
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
        }

        .alert-info i {
            font-size: 1.2rem;
            margin-right: 10px;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fad961 0%, #f76b1c 100%);
            border: none;
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(250, 217, 97, 0.3);
        }

        /* Form styling */
        .form-label {
            font-weight: 600;
            color: #4e73df;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .form-control, .form-select {
            border: 2px solid #e3e6f0;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .form-text {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .invalid-feedback {
            color: #dc3545;
            font-weight: 500;
        }

        /* Info box */
        .info-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 5px solid #4e73df;
        }

        .info-box h6 {
            color: #4e73df;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Peminjaman item styling */
        .peminjaman-option {
            padding: 10px;
            border-bottom: 1px solid #f0f2f5;
        }

        .peminjaman-option:last-child {
            border-bottom: none;
        }

        /* Telat indicator */
        .telat-badge {
            background: linear-gradient(135deg, #ff5858 0%, #f09819 100%);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-left: 10px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-header-custom {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .btn-add, .btn-back {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div id="main">
        <!-- Main Card -->
        <div class="main-card">
            <!-- Card Header -->
            <div class="card-header-custom d-flex align-items-center justify-content-between">
                <h2 class="page-title">
                    <i class="fas fa-plus-circle"></i>
                    Tambah Data Pengembalian
                </h2>
                <a href="./index.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Daftar
                </a>
            </div>
            
            <!-- Card Body -->
            <div class="card-body-custom">
                <!-- Session Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert-container">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert-container">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Info Box -->
                <div class="info-box">
                    <h6><i class="fas fa-info-circle"></i> INFORMASI PEMINJAMAN</h6>
                    <p class="mb-2">Pilih peminjaman yang akan dikembalikan dari daftar di bawah.</p>
                    
                    <?php if ($totalPeminjamanAktif == 0): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Tidak ada peminjaman aktif yang dapat dikembalikan.</strong>
                            <p class="mb-0 mt-2">Pastikan ada alat berat yang sedang dipinjam (status: dipinjam) dan belum direkam pengembaliannya.</p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Ditemukan <?php echo $totalPeminjamanAktif; ?> peminjaman aktif.</strong>
                            <p class="mb-0 mt-2">Pilih salah satu peminjaman di bawah untuk proses pengembalian.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Pengembalian Form -->
                <form action="../../action/pengembalian/store.php" method="POST" class="needs-validation" novalidate>
                    <!-- Peminjaman Selection -->
                    <div class="mb-4">
                        <label for="peminjaman_id" class="form-label">Peminjaman *</label>
                        <select name="peminjaman_id" class="form-select" id="peminjaman_id" required
                                <?php echo ($totalPeminjamanAktif == 0) ? 'disabled' : ''; ?>>
                            <option value="">Pilih Peminjaman</option>
                            <?php foreach ($peminjamanList as $peminjaman): 
                                // Hitung hari telat jika ada
                                $telatHari = 0;
                                $hariIni = new DateTime();
                                $tglRencana = new DateTime($peminjaman['tgl_kembali_rencana']);
                                
                                if ($hariIni > $tglRencana) {
                                    $selisih = $tglRencana->diff($hariIni);
                                    $telatHari = $selisih->days;
                                }
                            ?>
                                <option value="<?= $peminjaman['id'] ?>" 
                                        data-tgl-rencana="<?= $peminjaman['tgl_kembali_rencana'] ?>"
                                        data-tgl-pinjam="<?= $peminjaman['tgl_pinjam'] ?>"
                                        data-jumlah-pinjam="<?= $peminjaman['jumlah_pinjam'] ?>">
                                    #<?= $peminjaman['id'] ?> - <?= htmlspecialchars($peminjaman['kode_peminjaman'] ?? '') ?> 
                                    (<?= htmlspecialchars($peminjaman['nama_peminjam'] ?? '') ?> - <?= htmlspecialchars($peminjaman['nama_barang'] ?? '') ?>)
                                    <?php if ($telatHari > 0): ?>
                                        <span class="telat-badge">Telat <?= $telatHari ?> hari</span>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Harap pilih peminjaman</div>
                        <?php if ($totalPeminjamanAktif == 0): ?>
                            <div class="form-text text-danger">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Tidak ada peminjaman aktif. Pastikan ada alat yang sedang dipinjam.
                            </div>
                        <?php else: ?>
                            <div class="form-text">
                                Pilih peminjaman yang akan dikembalikan dari daftar di atas
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Tanggal Kembali -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="tgl_kembali" class="form-label">Tanggal Kembali *</label>
                            <input type="date" name="tgl_kembali" class="form-control" id="tgl_kembali" required
                                   min="<?= date('Y-m-d') ?>">
                            <div class="invalid-feedback">Harap isi tanggal kembali</div>
                            <div class="form-text" id="tgl_kembali_info">Tanggal pengembalian alat</div>
                        </div>
                        <div class="col-md-6">
                            <label for="kondisi" class="form-label">Kondisi Alat *</label>
                            <select name="kondisi" class="form-select" id="kondisi" required>
                                <option value="">Pilih Kondisi</option>
                                <option value="baik">Baik</option>
                                <option value="rusak_ringan">Rusak Ringan</option>
                                <option value="rusak_berat">Rusak Berat</option>
                            </select>
                            <div class="invalid-feedback">Harap pilih kondisi alat</div>
                            <div class="form-text">Kondisi alat saat dikembalikan</div>
                        </div>
                    </div>
                    
                    <!-- Denda -->
                    <div class="mb-4">
                        <label for="denda" class="form-label">Denda (Rp)</label>
                        <input type="number" name="denda" class="form-control" id="denda"
                               placeholder="0" min="0" step="1000" value="0">
                        <div class="invalid-feedback">Denda tidak boleh negatif</div>
                        <div class="form-text">Isi jika ada denda keterlambatan/kerusakan</div>
                        <div class="mt-2">
                            <div id="denda_info" class="text-muted"></div>
                            <div id="telat_info" class="text-danger fw-bold" style="display: none;"></div>
                        </div>
                    </div>
                    
                    <!-- Keterangan -->
                    <div class="mb-4">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" id="keterangan" 
                                  rows="3" placeholder="Tambahkan keterangan jika perlu..."></textarea>
                        <div class="form-text">Deskripsi kondisi atau alasan denda (jika ada)</div>
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-between mt-5">
                        <a href="./index.php" class="btn btn-back">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-submit" name="tombol"
                                <?php echo ($totalPeminjamanAktif == 0) ? 'disabled' : ''; ?>>
                            <i class="fas fa-save"></i> Simpan Pengembalian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../partials/script.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Validasi form
(function() {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();

// Set tanggal minimal hari ini
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const todayStr = today.toISOString().split('T')[0];
    document.getElementById('tgl_kembali').value = todayStr;
    document.getElementById('tgl_kembali').min = todayStr;
});

// Fungsi untuk menghitung denda otomatis
function hitungDendaOtomatis(tglRencana, tglKembali) {
    const tgl1 = new Date(tglRencana);
    const tgl2 = new Date(tglKembali);
    
    if (tgl2 > tgl1) {
        const diffTime = Math.abs(tgl2 - tgl1);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        // Hitung denda: Rp 100,000 per hari telat
        const dendaPerHari = 100000;
        const totalDenda = diffDays * dendaPerHari;
        
        return {
            telatHari: diffDays,
            totalDenda: totalDenda
        };
    }
    
    return {
        telatHari: 0,
        totalDenda: 0
    };
}

// Update info ketika peminjaman dipilih
document.getElementById('peminjaman_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const tglRencana = selectedOption.getAttribute('data-tgl-rencana');
    const tglPinjam = selectedOption.getAttribute('data-tgl-pinjam');
    const jumlahPinjam = selectedOption.getAttribute('data-jumlah-pinjam');
    
    if (tglRencana) {
        // Format tanggal untuk ditampilkan
        const tglRencanaFormatted = new Date(tglRencana).toLocaleDateString('id-ID');
        const tglPinjamFormatted = new Date(tglPinjam).toLocaleDateString('id-ID');
        
        // Update info
        document.getElementById('tgl_kembali_info').innerHTML = 
            `Rencana kembali: ${tglRencanaFormatted} | Tanggal pinjam: ${tglPinjamFormatted}`;
        
        // Reset denda info
        document.getElementById('telat_info').style.display = 'none';
        document.getElementById('denda').value = 0;
    }
});

// Update denda ketika tanggal kembali diubah
document.getElementById('tgl_kembali').addEventListener('change', function() {
    const selectedOption = document.getElementById('peminjaman_id').options[document.getElementById('peminjaman_id').selectedIndex];
    const tglRencana = selectedOption.getAttribute('data-tgl-rencana');
    const tglKembali = this.value;
    
    if (tglRencana && tglKembali) {
        const dendaInfo = hitungDendaOtomatis(tglRencana, tglKembali);
        
        if (dendaInfo.telatHari > 0) {
            document.getElementById('telat_info').style.display = 'block';
            document.getElementById('telat_info').innerHTML = 
                `<i class="fas fa-exclamation-triangle"></i> Telat ${dendaInfo.telatHari} hari. Denda otomatis: Rp ${dendaInfo.totalDenda.toLocaleString('id-ID')}`;
            
            // Set nilai denda otomatis
            document.getElementById('denda').value = dendaInfo.totalDenda;
        } else {
            document.getElementById('telat_info').style.display = 'none';
            document.getElementById('denda').value = 0;
        }
    }
});

// Alert jika tidak ada peminjaman
<?php if ($totalPeminjamanAktif == 0): ?>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        alert('Tidak ada peminjaman aktif yang dapat dikembalikan.\n\nPastikan:\n1. Ada alat yang sedang dipinjam (status: dipinjam)\n2. Peminjaman belum direkam pengembaliannya\n3. Status peminjaman sudah sesuai');
    }, 500);
});
<?php endif; ?>
</script>

</body>
</html>